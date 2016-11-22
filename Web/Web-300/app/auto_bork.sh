while true; do
    python bork.py&
    PID=$!
    sleep 120
    kill -9 $PID
done
