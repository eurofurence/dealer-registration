#!/bin/sh

# Start the server
/usr/bin/docker-entrypoint.sh server /data/minio --console-address ":8900" &

# This is unfortunately the only reasonably sure way to confirm server starts
sleep 5

/usr/bin/mc alias set ef-minio "${AWS_ENDPOINT}" "${AWS_ACCESS_KEY_ID}" "${AWS_SECRET_ACCESS_KEY}";
/usr/bin/mc ping --exit --count 5 --interval 1 ef-minio
/usr/bin/mc ready ef-minio
/usr/bin/mc mb -p ef-minio/"${AWS_BUCKET}"
/usr/bin/mc anonymous set download "ef-minio/${AWS_BUCKET}"
sleep infinity
