"""
GBI Pondok Daud AI Service
- Sistem Rekomendasi: Content-Based + Collaborative Filtering
- Chatbot RAG: TF-IDF retrieval + template/LLM generation
"""

import math
import os
import re
from contextlib import asynccontextmanager
from typing import Any

import numpy as np
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# ─── Bobot Content-Based Filtering (untuk dokumentasi tesis) ───
WEIGHTS = {
    "minat": 0.30,
    "bakat": 0.25,
    "usia": 0.15,
    "ketersediaan": 0.20,
    "lokasi": 0.10,
}
ALPHA_CONTENT = 0.60
BETA_COLLAB = 0.40

ALL_MINAT = [
    "musik", "multimedia", "teknologi", "melayani", "anak_muda",
    "doa", "evangelisme", "sosial", "administrasi", "kreatif",
]
ALL_BAKAT = [
    "vokal", "gitar", "keyboard", "drum", "fotografi", "video",
    "desain", "public_speaking", "organisasi", "komputer",
]
ALL_HARI = ["senin", "selasa", "rabu", "kamis", "jumat", "sabtu", "minggu"]

tfidf_vectorizer = TfidfVectorizer(max_features=5000, ngram_range=(1, 2))
knowledge_corpus: list[str] = []
knowledge_meta: list[dict] = []
tfidf_matrix = None


@asynccontextmanager
async def lifespan(_app: FastAPI):
    print("[AI Service] Siap — Recommendation + RAG Chatbot")
    yield


app = FastAPI(title="GBI Pondok Daud AI Service", version="1.0.0", lifespan=lifespan)
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])


# ─── Schemas ───

class UserProfileInput(BaseModel):
    usia: int = 25
    kecamatan: str = ""
    minat: list[str] = Field(default_factory=list)
    bakat: list[str] = Field(default_factory=list)
    ketersediaan_hari: list[str] = Field(default_factory=list)
    ketersediaan_waktu: str = "fleksibel"


class TimItem(BaseModel):
    id: int
    nama: str
    minat_tags: list[str]
    bakat_tags: list[str]
    min_usia: int = 17
    max_usia: int = 65
    hari_pelayanan: list[str]


class CoolItem(BaseModel):
    id: int
    nama: str
    kecamatan: str
    tema_minat: list[str]
    min_usia: int = 17
    max_usia: int = 60
    jadwal: str = ""


class OtherUser(BaseModel):
    user_id: int
    minat: list[str] = Field(default_factory=list)
    bakat: list[str] = Field(default_factory=list)
    kecamatan: str = ""
    pelayanan_ids: list[int] = Field(default_factory=list)
    cool_ids: list[int] = Field(default_factory=list)


class RecommendRequest(BaseModel):
    profile: UserProfileInput
    tim_pelayanan: list[TimItem]
    cool_groups: list[CoolItem]
    other_users: list[OtherUser] = Field(default_factory=list)
    top_k: int = 3


class KnowledgeDoc(BaseModel):
    id: str
    title: str
    content: str
    category: str = "umum"


class ChatRequest(BaseModel):
    message: str
    documents: list[KnowledgeDoc] = Field(default_factory=list)
    history: list[dict] = Field(default_factory=list)
    openai_api_key: str = ""


# ─── Recommendation Helpers ───

def jaccard(a: set, b: set) -> float:
    if not a and not b:
        return 0.0
    inter = len(a & b)
    union = len(a | b)
    return inter / union if union else 0.0


def age_score(user_age: int, min_age: int, max_age: int) -> float:
    if min_age <= user_age <= max_age:
        return 1.0
    dist = min(abs(user_age - min_age), abs(user_age - max_age))
    return max(0.0, 1.0 - dist / 20)


def location_score(user_kec: str, target_kec: str) -> float:
    if not user_kec or not target_kec:
        return 0.5
    u, t = user_kec.lower().strip(), target_kec.lower().strip()
    if u == t:
        return 1.0
    if u in t or t in u:
        return 0.7
    return 0.2


def to_vector(tags: list[str], universe: list[str]) -> np.ndarray:
    tag_set = {t.lower() for t in tags}
    return np.array([1.0 if u in tag_set else 0.0 for u in universe])


def cosine_vec(a: np.ndarray, b: np.ndarray) -> float:
    na, nb = np.linalg.norm(a), np.linalg.norm(b)
    if na == 0 or nb == 0:
        return 0.0
    return float(np.dot(a, b) / (na * nb))


def content_score_tim(profile: UserProfileInput, item: TimItem) -> dict:
    s_minat = jaccard(set(profile.minat), set(item.minat_tags))
    s_bakat = jaccard(set(profile.bakat), set(item.bakat_tags))
    s_usia = age_score(profile.usia, item.min_usia, item.max_usia)
    s_ketersediaan = jaccard(set(profile.ketersediaan_hari), set(item.hari_pelayanan))
    s_lokasi = 0.5

    total = (
        WEIGHTS["minat"] * s_minat
        + WEIGHTS["bakat"] * s_bakat
        + WEIGHTS["usia"] * s_usia
        + WEIGHTS["ketersediaan"] * s_ketersediaan
        + WEIGHTS["lokasi"] * s_lokasi
    )

    return {
        "total": round(total, 4),
        "minat": round(s_minat, 4),
        "bakat": round(s_bakat, 4),
        "usia": round(s_usia, 4),
        "ketersediaan": round(s_ketersediaan, 4),
        "lokasi": round(s_lokasi, 4),
    }


def content_score_cool(profile: UserProfileInput, item: CoolItem) -> dict:
    s_minat = jaccard(set(profile.minat), set(item.tema_minat))
    s_usia = age_score(profile.usia, item.min_usia, item.max_usia)
    s_lokasi = location_score(profile.kecamatan, item.kecamatan)

    total = 0.40 * s_minat + 0.25 * s_usia + 0.35 * s_lokasi

    return {
        "total": round(total, 4),
        "minat": round(s_minat, 4),
        "usia": round(s_usia, 4),
        "lokasi": round(s_lokasi, 4),
    }


def user_similarity(p: UserProfileInput, other: OtherUser) -> float:
    v1 = np.concatenate([
        to_vector(p.minat, ALL_MINAT),
        to_vector(p.bakat, ALL_BAKAT),
    ])
    v2 = np.concatenate([
        to_vector(other.minat, ALL_MINAT),
        to_vector(other.bakat, ALL_BAKAT),
    ])
    sim = cosine_vec(v1, v2)
    loc = location_score(p.kecamatan, other.kecamatan)
    return 0.8 * sim + 0.2 * loc


def collab_score_tim(item_id: int, others: list[OtherUser], profile: UserProfileInput) -> float:
    if not others:
        return 0.0
    sims = [(user_similarity(profile, o), o) for o in others]
    sims.sort(key=lambda x: x[0], reverse=True)
    top_k = sims[:5]
    weighted = sum(s * (1 if item_id in o.pelayanan_ids else 0) for s, o in top_k)
    max_possible = sum(s for s, _ in top_k) or 1
    return round(weighted / max_possible, 4)


def collab_score_cool(item_id: int, others: list[OtherUser], profile: UserProfileInput) -> float:
    if not others:
        return 0.0
    sims = [(user_similarity(profile, o), o) for o in others]
    sims.sort(key=lambda x: x[0], reverse=True)
    top_k = sims[:5]
    weighted = sum(s * (1 if item_id in o.cool_ids else 0) for s, o in top_k)
    max_possible = sum(s for s, _ in top_k) or 1
    return round(weighted / max_possible, 4)


# ─── RAG Chatbot Helpers ───

def build_knowledge_index(docs: list[KnowledgeDoc]):
    global knowledge_corpus, knowledge_meta, tfidf_matrix
    knowledge_corpus = [f"{d.title}. {d.content}" for d in docs]
    knowledge_meta = [{"id": d.id, "title": d.title, "category": d.category} for d in docs]
    if knowledge_corpus:
        tfidf_matrix = tfidf_vectorizer.fit_transform(knowledge_corpus)
    else:
        tfidf_matrix = None


def retrieve(query: str, top_k: int = 3) -> list[dict]:
    if tfidf_matrix is None or not knowledge_corpus:
        return []
    q_vec = tfidf_vectorizer.transform([query])
    sims = cosine_similarity(q_vec, tfidf_matrix).flatten()
    top_idx = sims.argsort()[::-1][:top_k]
    results = []
    for idx in top_idx:
        if sims[idx] > 0.05:
            results.append({
                **knowledge_meta[idx],
                "content": knowledge_corpus[idx][:500],
                "score": round(float(sims[idx]), 4),
            })
    return results


def detect_intent(msg: str) -> str:
    m = msg.lower()
    if any(w in m for w in ["pembaptisan", "baptis", "dibaptis"]):
        return "pembaptisan"
    if any(w in m for w in ["pernikahan", "menikah", "nikah"]):
        return "pernikahan"
    if any(w in m for w in ["ibadah", "jadwal", "jam", "waktu"]):
        return "jadwal"
    if any(w in m for w in ["cool", "sel", "komunitas", "kelompok"]):
        return "cool"
    if any(w in m for w in ["pelayanan", "melayani", "tim", "volunteer"]):
        return "pelayanan"
    if any(w in m for w in ["ayat", "renungan", "verse", "firman"]):
        return "renungan"
    if any(w in m for w in ["doa", "berdoa", "shalom"]):
        return "doa"
    return "umum"


def generate_response(message: str, sources: list[dict]) -> str:
    intent = detect_intent(message)

    if sources and sources[0]["score"] > 0.15:
        best = sources[0]
        content = best.get("content", "")
        snippet = content[:400].strip()
        if len(content) > 400:
            snippet += "..."
        return (
            f"Berdasarkan informasi gereja ({best['title']}):\n\n"
            f"{snippet}\n\n"
            f"Apakah ada hal lain yang ingin Anda tanyakan? 🙏"
        )

    responses = {
        "pembaptisan": (
            "Untuk pendaftaran **pembaptisan** di GBI Pondok Daud:\n\n"
            "1. Ikuti kelas KRK (Kelas Regenerasi Kejadian)\n"
            "2. Konsultasi dengan pendeta jemaat\n"
            "3. Daftar melalui sekretariat gereja atau portal GBI Pondok Daud\n\n"
            "Pembaptisan biasanya dilaksanakan setelah kelas KRK selesai. "
            "Silakan hubungi sekretariat untuk jadwal terbaru."
        ),
        "pernikahan": (
            "Untuk **pendaftaran pernikahan** di GBI Pondok Daud:\n\n"
            "1. Kedua mempelai sudah dibaptis selam\n"
            "2. Mengikuti konseling pra-nikah minimal 3 sesi\n"
            "3. Menyerahkan dokumen ke sekretariat minimal 1 bulan sebelumnya\n\n"
            "Hubungi pendeta jemaat untuk memulai proses konseling."
        ),
        "jadwal": (
            "Jadwal ibadah GBI Pondok Daud bisa Anda lihat di menu **Jadwal Ibadah** "
            "atau tanyakan spesifik, misalnya: 'Ibadah pemuda jam berapa?' "
            "Saya akan carikan informasi terkini dari database gereja."
        ),
        "cool": (
            "COOL (Christians Of Outstanding Love) adalah kelompok sel GBI Pondok Daud. "
            "Isi **Kuesioner Profil** di menu GBI Pondok Daud, lalu sistem AI akan merekomendasikan "
            "COOL terdekat sesuai domisili dan minat Anda."
        ),
        "pelayanan": (
            "Tim pelayanan GBI Pondok Daud meliputi: Multimedia, Praise & Worship, "
            "Usher, Youth, Doa, dan Tim Anak. Isi kuesioner profil untuk mendapat "
            "rekomendasi tim yang paling cocok dengan bakat dan minat Anda."
        ),
        "renungan": (
            "Tuhan berfirman: *'Janganlah hendaknya kamu kuatir tentang apapun juga, "
            "tetapi nyatakanlah dalam segala hal kehendakmu kepada Allah disertai "
            "ucapan syukur.'* — Filipi 4:6\n\n"
            "Lihat renungan lengkap di menu **Renungan & Warta** GBI Pondok Daud."
        ),
        "doa": (
            "Tuhan Yesus mendengar doa kita. Anda bisa mengirim **Permohonan Doa** "
            "melalui menu GBI Pondok Daud, dan tim pendoa GBI Pondok Daud akan mendoakan Anda. "
            "Shalom! 🙏"
        ),
        "umum": (
            "Shalom! Saya **Asisten GBI Pondok Daud**. "
            "Saya bisa membantu informasi seputar:\n"
            "• Jadwal ibadah\n• COOL / Kelompok Sel\n• Tim pelayanan\n"
            "• Pembaptisan & pernikahan\n• Renungan & ayat firman\n\n"
            "Silakan tanyakan apa saja! 🙏"
        ),
    }

    return responses.get(intent, responses["umum"])


# ─── Endpoints ───

@app.get("/health")
def health():
    return {
        "status": "ok",
        "services": ["recommendation", "chatbot_rag"],
        "algorithm": "Content-Based (60%) + Collaborative Filtering (40%)",
        "nlp": "TF-IDF RAG + Intent Detection",
    }


@app.post("/recommend")
def recommend(req: RecommendRequest):
    tim_results = []
    for item in req.tim_pelayanan:
        cs = content_score_tim(req.profile, item)
        col = collab_score_tim(item.id, req.other_users, req.profile)
        final = ALPHA_CONTENT * cs["total"] + BETA_COLLAB * col
        tim_results.append({
            "id": item.id,
            "nama": item.nama,
            "score": round(final, 4),
            "score_pct": round(final * 100, 1),
            "content_score": cs,
            "collab_score": col,
            "formula": f"{ALPHA_CONTENT}×content + {BETA_COLLAB}×collab",
        })

    cool_results = []
    for item in req.cool_groups:
        cs = content_score_cool(req.profile, item)
        col = collab_score_cool(item.id, req.other_users, req.profile)
        final = ALPHA_CONTENT * cs["total"] + BETA_COLLAB * col
        cool_results.append({
            "id": item.id,
            "nama": item.nama,
            "kecamatan": item.kecamatan,
            "score": round(final, 4),
            "score_pct": round(final * 100, 1),
            "content_score": cs,
            "collab_score": col,
            "formula": f"{ALPHA_CONTENT}×content + {BETA_COLLAB}×collab",
        })

    tim_results.sort(key=lambda x: x["score"], reverse=True)
    cool_results.sort(key=lambda x: x["score"], reverse=True)

    return {
        "tim_pelayanan": tim_results[: req.top_k],
        "cool_groups": cool_results[: req.top_k],
        "weights": WEIGHTS,
        "hybrid": {"alpha_content": ALPHA_CONTENT, "beta_collab": BETA_COLLAB},
    }


@app.post("/chat")
def chat(req: ChatRequest):
    if not req.message.strip():
        raise HTTPException(status_code=422, detail="Pesan tidak boleh kosong.")

    build_knowledge_index(req.documents)
    sources = retrieve(req.message, top_k=3)

    answer = generate_response(req.message, sources)

    if req.openai_api_key and sources:
        try:
            import urllib.request
            import json as json_lib

            context = "\n".join(s["content"][:300] for s in sources[:2])
            payload = json_lib.dumps({
                "model": "gpt-4o-mini",
                "messages": [
                    {"role": "system", "content": (
                        "Kamu asisten gereja GBI Pondok Daud. Jawab dalam Bahasa Indonesia "
                        "dengan hangat dan pastoral. Gunakan konteks berikut:\n" + context
                    )},
                    {"role": "user", "content": req.message},
                ],
                "max_tokens": 500,
            }).encode()
            request = urllib.request.Request(
                "https://api.openai.com/v1/chat/completions",
                data=payload,
                headers={
                    "Authorization": f"Bearer {req.openai_api_key}",
                    "Content-Type": "application/json",
                },
            )
            with urllib.request.urlopen(request, timeout=15) as resp:
                data = json_lib.loads(resp.read())
                answer = data["choices"][0]["message"]["content"]
        except Exception:
            pass

    return {
        "answer": answer,
        "sources": sources,
        "intent": detect_intent(req.message),
    }


if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("AI_SERVICE_PORT", "5002"))
    uvicorn.run("app:app", host="0.0.0.0", port=port, reload=False)
