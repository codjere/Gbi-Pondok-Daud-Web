"""
GBI Pondok Daud Face Recognition Service
FaceNet (InceptionResnetV1) + MTCNN — ekstraksi embedding 512-dimensi.
Dipanggil oleh portal GBI Pondok Daud untuk registrasi wajah & absensi pelayan.
"""

import base64
import io
import os
from contextlib import asynccontextmanager

import numpy as np
import torch
from facenet_pytorch import InceptionResnetV1, MTCNN
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from PIL import Image
from pydantic import BaseModel, Field

DEVICE = torch.device("cuda:0" if torch.cuda.is_available() else "cpu")
mtcnn_detector = None
resnet_model = None


@asynccontextmanager
async def lifespan(_app: FastAPI):
    global mtcnn_detector, resnet_model
    print(f"[FaceNet] Memuat model di perangkat: {DEVICE}")
    mtcnn_detector = MTCNN(image_size=160, margin=20, keep_all=False, device=DEVICE)
    resnet_model = InceptionResnetV1(pretrained="vggface2").eval().to(DEVICE)
    print("[FaceNet] Model siap.")
    yield


app = FastAPI(
    title="GBI Pondok Daud Face Recognition Service",
    description="Ekstraksi embedding wajah FaceNet untuk GBI Pondok Daud",
    version="1.0.0",
    lifespan=lifespan,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


class ImagePayload(BaseModel):
    image: str = Field(..., description="Base64 encoded JPEG/PNG image")


class MultiImagePayload(BaseModel):
    images: list[str] = Field(..., min_length=1, max_length=20)


def decode_image(b64: str) -> Image.Image:
    if "," in b64:
        b64 = b64.split(",", 1)[1]
    try:
        raw = base64.b64decode(b64)
        return Image.open(io.BytesIO(raw)).convert("RGB")
    except Exception as exc:
        raise HTTPException(status_code=400, detail=f"Gambar tidak valid: {exc}") from exc


def extract_single_embedding(img: Image.Image) -> np.ndarray | None:
    if mtcnn_detector is None or resnet_model is None:
        raise HTTPException(status_code=503, detail="Model belum siap.")

    face = mtcnn_detector(img)
    if face is None:
        return None

    face = face.unsqueeze(0).to(DEVICE)
    with torch.no_grad():
        embedding = resnet_model(face).squeeze(0).cpu().numpy()

    return embedding


@app.get("/health")
def health():
    return {
        "status": "ok",
        "device": str(DEVICE),
        "model": "InceptionResnetV1 (vggface2)",
        "embedding_dim": 512,
        "model_loaded": mtcnn_detector is not None and resnet_model is not None,
    }


@app.post("/extract-embedding")
def extract_embedding(payload: ImagePayload):
    img = decode_image(payload.image)
    embedding = extract_single_embedding(img)

    if embedding is None:
        raise HTTPException(status_code=422, detail="Wajah tidak terdeteksi. Pastikan wajah terlihat jelas.")

    return {
        "embedding": embedding.tolist(),
        "dimensions": len(embedding),
    }


@app.post("/extract-embedding-batch")
def extract_embedding_batch(payload: MultiImagePayload):
    vectors: list[np.ndarray] = []
    failed = 0

    for b64 in payload.images:
        img = decode_image(b64)
        vec = extract_single_embedding(img)
        if vec is not None:
            vectors.append(vec)
        else:
            failed += 1

    if not vectors:
        raise HTTPException(
            status_code=422,
            detail="Tidak ada wajah terdeteksi dari semua foto. Coba ulang dengan pencahayaan lebih baik.",
        )

    avg = np.mean(vectors, axis=0)

    return {
        "embedding": avg.tolist(),
        "dimensions": len(avg),
        "samples_used": len(vectors),
        "samples_failed": failed,
    }


if __name__ == "__main__":
    import uvicorn

    port = int(os.getenv("FACE_SERVICE_PORT", "5001"))
    uvicorn.run("app:app", host="0.0.0.0", port=port, reload=False)
