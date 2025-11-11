# API Documentation

## Endpoints

### 1. Sensor Ingestion (Production)
**POST** `/api/ingest_sensor.php`

Receives real-time sensor data from IoT gateway.

**Authentication:**
- Header: `X-API-Key: <RECEIVER_API_KEY>`
- Or query param: `?api_key=<RECEIVER_API_KEY>`

**Request Body (JSON):**
```json
{
  "nodeID": 1,
  "temperature": 28.5,
  "humidity": 60.2,
  "pressure": 1005.3,
  "accelX": 0.12,
  "accelY": -0.03,
  "accelZ": 9.81,
  "gyroX": 0.01,
  "gyroY": 0.00,
  "gyroZ": -0.02,
  "piezo1": 1200,
  "piezo2": 1180,
  "piezo3": 1210,
  "latitude": -5.55,
  "longitude": 105.33
}
```

**Response:**
```json
{
  "status": "success",
  "node_id": 1,
  "inserted_at": "2025-11-08 10:30:45",
  "reading_status": "NORMAL",
  "reading_status_class": "status-normal"
}
```

**Status Classification:**
- `NORMAL` - Vibration < 50000 AND MPU < 50000
- `PERINGATAN` - Vibration 50000-79999 OR MPU 50000-79999
- `BAHAYA` - Vibration ≥ 80000 OR MPU ≥ 80000

---

### 2. Get Latest Data
**GET** `/api/get-latest-data.php`

Returns the most recent sensor reading for each node (1-4).

**Response:**
```json
{
  "status": "success",
  "nodes": {
    "1": {
      "node_id": 1,
      "timestamp": "2025-11-08 10:30:45",
      "temperature": 28.5,
      "humidity": 60.2,
      "pressure": 1005.3,
      "vibration": 1196.67,
      "mpu6050": 9.81,
      "latitude": -5.55,
      "longitude": 105.33,
      "status": "NORMAL",
      "status_class": "status-normal",
      "battery": 85
    },
    ...
  }
}
```

---

### 3. Get Node Historical Data
**GET** `/api/get-node-data.php`

Returns time-series data for chart visualization.

**Query Parameters:**
- `node_id` (optional) - Specific node ID (1-4). If omitted, returns data for all nodes
- `hours` (optional, default: 1, max: 168) - Time range in hours (max 7 days)
- `sensors` (optional, default: vibration,acceleration) - Comma-separated sensor types
  - Valid values: `vibration`, `acceleration`, `temperature`, `humidity`, `pressure`, `battery`

**Example:**
```
GET /api/get-node-data.php?node_id=1&hours=6&sensors=vibration,acceleration,temperature
```

**Response (single node):**
```json
{
  "status": "success",
  "vibration": [
    {"x": "2025-11-08 10:00:00", "y": 1200},
    {"x": "2025-11-08 10:05:00", "y": 1185},
    ...
  ],
  "acceleration": [
    {"x": "2025-11-08 10:00:00", "y": 9.82},
    ...
  ],
  "recent_readings": [
    {
      "timestamp": "2025-11-08 10:30:00",
      "vibration": 1196,
      "acceleration": 9.81,
      "temperature": 28.5,
      "humidity": 60.2,
      "pressure": 1005.3
    },
    ...
  ]
}
```

**Response (all nodes):**
```json
{
  "status": "success",
  "node1": {
    "vibration": [...],
    "acceleration": [...]
  },
  "node2": {
    "vibration": [...],
    "acceleration": [...]
  },
  ...
}
```

---

## Error Responses

All endpoints return consistent error format:

```json
{
  "status": "error",
  "message": "Error description"
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `400` - Bad Request (missing required fields)
- `401` - Unauthorized (invalid API key)
- `405` - Method Not Allowed
- `500` - Internal Server Error

---

## Security

- Ingestion endpoint requires API key authentication
- All endpoints use `Content-Type: application/json`
- Cache headers prevent stale data (`Cache-Control: no-cache, must-revalidate`)
- Input validation and sanitization applied
- PDO prepared statements prevent SQL injection
- Error details logged server-side only (not exposed to client)

---

## Database Schema

Expected `sensor_data` table structure:
- `id` - Primary key
- `node_id` - INT (1-4)
- `timestamp` - DATETIME
- `vibration` - FLOAT (computed from piezo average)
- `mpu6050` - FLOAT (acceleration magnitude)
- `temperature` - FLOAT
- `humidity` - FLOAT
- `pressure` - FLOAT
- `latitude` - FLOAT
- `longitude` - FLOAT
- `accel_x`, `accel_y`, `accel_z` - FLOAT
- `gyro_x`, `gyro_y`, `gyro_z` - FLOAT
- `piezo_1`, `piezo_2`, `piezo_3` - INT
- `battery` - FLOAT (optional)

---

## Configuration

Thresholds configured via environment variables in `includes/config.php`:
- `THRESHOLD_VIB_WARN` - Vibration warning threshold
- `THRESHOLD_VIB_DANGER` - Vibration danger threshold
- `THRESHOLD_MPU_WARN` - MPU6050 warning threshold
- `THRESHOLD_MPU_DANGER` - MPU6050 danger threshold

Status computed by shared function: `computeStatus(vibration, mpu)`
