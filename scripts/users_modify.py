import mysql.connector
import pprint

DB = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

# Create cursors
cursor = DB.cursor(dictionary=True)
def getNumber(category,id):
    cursor.execute("SELECT old_id FROM maps WHERE new_id=%s", (id,))
    id = cursor.fetchone()
    return f"EIK/{str(getCategoryNumber(category))}/{id['old_id']}"

def getCategoryNumber(category):
    category = category.lower()
    if category == 'student':
        return 5
    if category == 'associate':
        return 2
    if category == 'fellow':
        return 7
    if category == 'honorary':
        return 6
    if category == 'affiliate':
        return 4
    if category == 'lead':
        return 1
    if category == 'corporate':
        return 3
    if category == 'firms':
        return 3
    else:
        raise Exception("Invalid category")

# Select all users from DB1
cursor.execute("SELECT * FROM users")
users = cursor.fetchall()

TOTAL = len(users)
COUNT = 0
SUCCESS = 0
ERRORED = 0

for user in users:
    COUNT += 1
    try:
        category = ''
        if user['role']=='Admin':
            raise Exception("Skipping admin user :)")
        if user['role'] == 'Individual':
            cursor.execute("SELECT * FROM individuals WHERE user_id=%s", (user['id'],))
            individual = cursor.fetchone()
            category = individual['category']
        if user['role'] == 'Firm':
            category = 'corporate'
        cursor.execute("UPDATE users SET number = %s WHERE id = %s", (getNumber(category, user['id']), user['id']))
        DB.commit()
        print(f"{COUNT}/{TOTAL} :: {round(COUNT/TOTAL*100, 2)}% :: {ERRORED} errored", end='\r')
        SUCCESS += 1
    except Exception as e:
        with open('users_update_logs.txt', 'a') as logs:
                logs.write(f"[{user['id']}]failed to modify error :: {e}\n")
        ERRORED += 1

print(f"{COUNT}/{TOTAL} :: {SUCCESS}({round(SUCCESS/TOTAL*100, 2)}%) success :: {round(ERRORED/TOTAL*100, 2)}% errored")