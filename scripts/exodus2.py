import mysql.connector

"""
query for modified tables
ALTER TABLE mpesas MODIFY COLUMN AccountReference VARCHAR(255) NULL, MODIFY COLUMN CheckoutRequestID VARCHAR(255) NULL, MODIFY COLUMN phone VARCHAR(255) NULL;
ALTER TABLE certificates DROP COLUMN expiry;
ALTER TABLE certificates ADD COLUMN year YEAR NULL;
SHOW INDEX FROM certificates;
ALTER TABLE certificates DROP index certificates_number_unique;
UPDATE certificates SET year = 2024;
ALTER TABLE mpesas ADD COLUMN receipt VARCHAR(255) NULL;
"""
DB = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel",
    charset='utf8mb4',
    collation='utf8mb4_unicode_ci'
)

# Create cursors
cursor = DB.cursor(dictionary=True, buffered=True)


def insertCert(user, number, year):
    cursor.execute("SELECT * FROM certificates WHERE number=%s AND year=%s", (number, year))
    certificate = cursor.fetchone()
    if certificate:
        return
    if year == '2025':
        cursor.execute(
            "INSERT INTO certificates (user_id, number, year, verified, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s)",
            (user, number, year, None, '2024-12-31', '2024-12-31')
        )
    else:
        cursor.execute(
            "INSERT INTO certificates (user_id, number, year, verified, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s)",
            (user, number, year, f"{year}-05-08", f"{year}-05-08", f"{year}-05-08")
        )
    DB.commit()

files = ['2022-EIK.csv','2023-EIK.csv','2024-EIK.csv','2025-EIK.csv']
missing = 0
errored = 0

for file in files:
    year = file.split('-')[0]
    members = 0
    identified = 0
    with open(file, 'r') as f:
        lines = f.readlines()
        for line in lines:
            try:
                members += 1
                line = line.strip().split(',')
                number = line[2]
                email = line[3].replace(';', '')
                if "EIK/" in number:
                    number = number.split('/')[2]
                else:
                    number = None
                
                cursor.execute("SELECT * FROM users WHERE email=%s", (email,))
                user = cursor.fetchone()
                if user:
                    identified += 1
                elif(number):
                    cursor.execute("SELECT * FROM users WHERE number LIKE %s", (f"%/{number}",))
                    user = cursor.fetchone()
                    if user:
                        identified += 1
                elif not user:
                    cursor.execute("SELECT * FROM individuals WHERE alternate=%s", (email,))
                    user = cursor.fetchone()
                    if user:
                        identified += 1
                        cursor.execute("SELECT * FROM users WHERE id=%s", (user['user_id'],))
                        user = cursor.fetchone()
                elif not user:
                    cursor.execute("SELECT * FROM firms WHERE alternate=%s", (email,))
                    user = cursor.fetchone()
                    if user:
                        identified += 1
                        cursor.execute("SELECT * FROM users WHERE id=%s", (user['user_id'],))
                        user = cursor.fetchone()
                if not user:
                    missing += 1
                    continue
                    raise Exception(f"User not found")
                print(f"processing {file} :: {members}/{len(lines)}", end='\r')
                #check if user is firm or individual
                cursor.execute("SELECT * FROM individuals WHERE user_id=%s", (user['id'],))
                individual = cursor.fetchone()
                cursor.execute("SELECT * FROM firms WHERE user_id=%s", (user['id'],))
                firm = cursor.fetchone()
                if individual:
                    phone = individual['phone']
                elif firm:
                    phone = firm['phone']
                #structure the user object to contain user_id, email, phone, and number
                user = {
                    "user_id": user['id'],
                    "email": email,
                    "phone": phone,
                    "number": user['number']
                }
                #check registration and subscription columns in file
                registration = line[4].replace("'", "").replace(",", "").replace(" ", "").lower()
                subscription = line[5].replace("'", "").replace(",", "").replace(" ", "").lower()
                receipt_no = line[6]
                tx_no = line[7]
                if "ppy" in subscription or "waiver" in subscription or "paid" in subscription or "pny" in subscription:
                    registration = 0
                    subscription = 0
                    # Insert into certificates table
                    insertCert(user['user_id'], user['number'], year)
                else:
                    try:
                        if registration != '':
                            registration = int(registration)
                        else:
                            registration = 0
                        if subscription != '':
                            subscription = int(subscription)
                        else:
                            subscription = 0
                    except:
                        raise Exception(f"Invalid registration or subscription value")
                if registration>0 or subscription>0:
                    if receipt_no=='' and False:
                        raise Exception(f"Missing receipt number")
                    if tx_no=='' and False:
                        raise Exception(f"Missing transaction code")
                    #TODO: Add to mpesa table
                    insertCert(user['user_id'], user['number'], year)
                    pass
            except Exception as e:
                errored += 1
                print(f"Error: {e} at {line}")
                # with open(f"{file.split('-')[0]}-missing.txt", 'a') as log_file:
                #     log_file.write(f"Error -> {line[0]} :: {line[1]} :: {line[2]} :: {email}\n") #name :: nema :: number :: email
    print(f"{identified}/{members} identified users in {file}")
print(f"Average missing users: {missing/len(files)}")
print(f"Total errors: {errored}")