from fastapi import FastAPI, UploadFile, File, Form
from fastapi.responses import JSONResponse
import face_recognition
import numpy as np
from io import BytesIO
from typing import List, Tuple

app = FastAPI()

@app.post("/image-embedding/")
async def image_embedding(file: UploadFile = File(...)):
    try:
        # Read file into memory
        contents = await file.read()

        # Load image from bytes
        image = face_recognition.load_image_file(BytesIO(contents))

        # Detect face locations
        locations: List[Tuple[int, int, int, int]] = face_recognition.face_locations(image)

        # Generate face embeddings for detected locations
        embeddings = face_recognition.face_encodings(image, known_face_locations=locations)

        if len(embeddings) == 0:
            # No face found
            return JSONResponse({
                "faces": [],
                "message": "No face detected in the image",
                "filename": file.filename,
            })

        # Convert all face embeddings (128-d) to lists
        faces = [e.tolist() for e in embeddings]

        # Return as JSON
        return JSONResponse({
            "faces": faces,
            "filename": file.filename,
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
        # Load new image and generate embedding
        contents = await file.read()
        image = face_recognition.load_image_file(BytesIO(contents))
        embeddings = face_recognition.face_encodings(image)

        if len(embeddings) == 0:
            return JSONResponse({
                "match": False,
                "message": "No face detected in the image"
            })

        new_embedding = embeddings[0]

        # Load DB embeddings from JSON string
        import json
        import json
        per_photo_faces = json.loads(db_embeddings)  # expected: List[List[embedding]]

        # Simple two-loop comparison: photos → embeddings
        results = []
        for idx, photo_embeddings in enumerate(per_photo_faces):
            if not isinstance(photo_embeddings, list):
                continue
            best_distance = None
            best_similarity = None
            for emb in photo_embeddings:
                distance = float(np.linalg.norm(new_embedding - np.array(emb)))
                similarity = float(1 - distance)
                if best_similarity is None or similarity > best_similarity:
                    best_similarity = similarity
                    best_distance = distance
            if best_similarity is not None and best_similarity >= 0.5:
                results.append({
                    "index": idx,
                    "distance": best_distance,
                    "similarity": best_similarity,
                })

        # Find best match among filtered results
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