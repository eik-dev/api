import os
import shutil

def move(origin, destination):
    try:
        os.makedirs(os.path.dirname(destination), exist_ok=True)
        shutil.move(origin, destination)
        return f"Moved file from {origin} to {destination}"
    except Exception as e:
        return f"Failed to move from {origin} to {destination}: {str(e)}"

if __name__ == "__main__":
    move("../public/uploads/test/x.pdf","../public/uploads/5/requirements/")