from fastapi import FastAPI, UploadFile, File, Form
from fastapi.responses import JSONResponse
import numpy as np
from io import BytesIO
import json
import insightface
from insightface.app import FaceAnalysis
from PIL import Image

app = FastAPI()

# Initialize the model once on startup
face_app = FaceAnalysis(name='buffalo_l', providers=['CoreMLExecutionProvider'])
face_app.prepare(ctx_id=0)

@app.post("/image-embedding/")
async def image_embedding(file: UploadFile = File(...)):
    try:
        contents = await file.read()

        # Load raw bytes → Convert to PIL image → Ensure RGB → Convert to NumPy array → Ready to feed into face recognition model.
        pil_img = Image.open(BytesIO(contents)).convert("RGB")
        img = np.array(pil_img)

        # Detect faces
        faces = face_app.get(img)

        if len(faces) == 0:
            return JSONResponse({
                "faces": [],
                "message": "No face detected in the image",
                "filename": file.filename,
            })

        # Extract embeddings for all faces
        embeddings = [face.embedding.tolist() for face in faces]

        return JSONResponse({
            "faces": embeddings,
            "filename": file.filename,
            "count": len(embeddings),
        })

    except Exception as e:
        return JSONResponse({
            "faces": [],
            "error": str(e)
        })


@app.post("/compare-face/")
async def compare_face(
    file: UploadFile = File(...),
    db_embeddings: str = Form(...)
):
    try:
        contents = await file.read()
        pil_img = Image.open(BytesIO(contents)).convert("RGB")
        img = np.array(pil_img)
        faces = face_app.get(img)

        if len(faces) == 0:
            return JSONResponse({
                "match": False,
                "message": "No face detected in the image"
            })

        # Take first detected face embedding from uploaded image
        new_embedding = faces[0].embedding

        # Parse DB embeddings (array of arrays of embeddings)
        per_photo_faces = json.loads(db_embeddings)

        results = []
        for idx, photo_embeddings in enumerate(per_photo_faces):
            if not isinstance(photo_embeddings, list):
                continue

            best_distance = None
            best_similarity = None

            for emb in photo_embeddings:
                # Cosine similarity (better for InsightFace)
                emb = np.array(emb)
                similarity = float(np.dot(new_embedding, emb) / (np.linalg.norm(new_embedding) * np.linalg.norm(emb)))
                distance = 1 - similarity

                if best_similarity is None or similarity > best_similarity:
                    best_similarity = similarity
                    best_distance = distance

            # Threshold: similarity >= 0.5 (≈80% match)
            if best_similarity is not None and best_similarity >= 0.5:
                results.append({
                    "index": idx,
                    "distance": best_distance,
                    "similarity": best_similarity,
                })

        if results:
            best_match = max(results, key=lambda x: x["similarity"])
            match = True
        else:
            best_match = None
            match = False

        return JSONResponse({
            "match": match,
            "best_match_index": best_match["index"] if best_match else None,
            "best_distance": best_match["distance"] if best_match else None,
            "all_results": results
        })

    except Exception as e:
        return JSONResponse({
            "match": False,
            "error": str(e)
        })
