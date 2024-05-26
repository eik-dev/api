import os
import shutil

def move(origin, destination, id):
    try:
        os.makedirs(os.path.dirname(destination), exist_ok=True)
        shutil.move(origin, destination)
        return f"Moved file from {origin} to {destination}"
    except Exception as e:
        with open('filelogs.txt', 'a') as logs:
            pass
            logs.write(f"Failed to move from {origin} to {destination}: {str(e)} :: UserID [{id}]\n")

if __name__ == "__main__":
    move("../public/uploads/test/x.pdf","../public/uploads/5/requirements/")