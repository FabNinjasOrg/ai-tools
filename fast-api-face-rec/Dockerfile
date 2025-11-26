FROM python:3.10-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1

# System dependencies required by insightface/onnxruntime and image handling
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    libgl1 \
    libglib2.0-0 \
    curl \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Install Python dependencies
RUN pip install \
    fastapi \
    "uvicorn[standard]" \
    numpy \
    pillow \
    python-multipart \
    insightface \
    onnxruntime

# Copy application code
COPY . /app

EXPOSE 8005

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8005"]
