import mysql.connector
import pprint
import random
from move import move

"""
######################### USERS ############################
id usr pwd access_lvl practising active approved can_print r_print activation_code date_created date_modified 
######################### USER_DETAILS ############################
id user_id category aemailadd foepin foe nemano emailadd first_name middle_name tel idno paddress county nationality town fax date_created last_updated 
######################### INTRO ############################
id user_id intro date_modified 
######################### CV_UPLOADED ############################
id uid filename title date_uploaded 
######################### COLLEGES ############################
id title institution country date_start date_end certification course courseL grade uid active 
######################### PROFQ ############################
id title institution city country date_start date_end certification course certB grade uid active 
######################### EXPERIENCES ############################
id uid job_title company date_from date_to salary telephone emailadd duties achievements date_modified active 
"""

def getType(category):
    if category == 'Corporate' or category == 'Firm' or category == '':
        return 'Firm'
    else:
        return 'Individual'
def getNumber(category):
    category = category.lower()
    if category == 'student':
        return 1
    if category == 'associate':
        return 2
    if category == 'fellow':
        return 3
    if category == 'honorary':
        return 4
    if category == 'affiliate':
        return 5
    if category == 'lead':
        return 6
    if category == 'corporate':
        return 7
    if category == 'firms':
        return 8
    else:
        raise Exception("Invalid category")

originPath = '../public/uploads/cvs/'
original = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="original_eik"
)

new = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

# Create cursors
originalC = original.cursor(dictionary=True)
newC = new.cursor()

# Select all users from DB1
originalC.execute("SELECT * FROM users")
users = originalC.fetchall()

TOTAL = len(users)
COUNT = 0
SUCCESS = 0
ERRORED = 0

# Migrate users to DB2
for user in users:
    try:
        USER = {
            'id': user['id'],
            'username': user['usr'],
            'name': None,
            'password': user['pwd'],
            'approved': user['approved'],
            'active': user['active'],
            'type': None,
            'email': None,
            'nema': None,
            'profile': {
                'category': None,
                'postal': None,
                'county': None,
                'nationalID': None,
                'nationality': None,
                'town': None,
                'alternate': None,
                'kra': None,
                'phone': None,
                'firm': None,
                'note': None
            },
            'certificate': {
                'requested': user['r_print'],
                'approved': user['can_print'],
            },
            'files': [],
            'education': [],
            'experience':[]
        }
        #fetch from user_details table where user_id = user[0]
        originalC.execute("SELECT * FROM user_details where user_id=%s", (user['id'],))
        user_details = originalC.fetchall()
        if len(user_details)>0:
            for detail in user_details:
                USER['type'] = getType(detail['category'])
                USER['email'] = detail['emailadd']
                USER['name'] = detail['first_name']
                USER['nema'] = detail['nemano']
                if getType(detail['category']) == 'Firm':
                    USER['profile'] = {
                        'category': detail['category'] if detail['category'] != '' else 'Corporate',
                        'postal': detail['paddress'],
                        'county': detail['county'],
                        'nationality': detail['nationality'],
                        'town': detail['town'],
                        'alternate': detail['aemailadd'],
                        'kra': detail['foepin'] if (detail['foepin'] != '' or detail['foepin'] != 'N/A') else random.randint(10000000, 99999999),
                        'phone': detail['tel']
                    }
                else:
                    USER['profile'] = {
                        'category': detail['category'],
                        'nationalID': detail['idno'] if (detail['idno'] != '' or detail['idno'] != 'N/A') else random.randint(10000000, 99999999),
                        'postal': detail['paddress'],
                        'county': detail['county'],
                        'nationality': detail['nationality'],
                        'town': detail['town'],
                        'alternate': detail['aemailadd'],
                        'firm': detail['foepin'],
                        'phone': detail['tel']
                    }
        else:
            raise Exception("Missing user_details")
        #fetch from intro table where user_id = user[0]
        originalC.execute("SELECT * FROM intro where user_id=%s", (user['id'],))
        intros = originalC.fetchall()
        if intros:
            USER['profile']['note'] = intros[0]['intro']
        else:
            USER['profile']['note'] = ''
        #fetch from cv_uploaded table where user_id = user['id']
        originalC.execute("SELECT * FROM cv_uploaded where uid=%s", (user['id'],))
        files = originalC.fetchall()
        for file in files:
            USER['files'].append({
                'filename': file['filename'],
                'title': file['title']
            })
        #fetch from user_dcollegesetails table where user_id = user['id']
        originalC.execute("SELECT * FROM colleges where uid=%s", (user['id'],))
        colleges = originalC.fetchall()
        for college in colleges:
            USER['education'].append({
                'title': college['title'],
                'institution': college['institution'],
                'certification': college['course'],
                'start': college['date_start'],
                'end': college['date_end'],
            })
        #fetch from profq table where user_id = user['id']
        originalC.execute("SELECT * FROM profq where uid=%s", (user['id'],))
        professions = originalC.fetchall()
        for profession in professions:
            USER['education'].append({
                'title': profession['title'],
                'institution': profession['institution'],
                'certification': profession['certification'],
                'start': profession['date_start'],
                'end': profession['date_end'],
            })
        #fetch from experiences table where user_id = user['id']
        originalC.execute("SELECT * FROM experiences where uid=%s", (user['id'],))
        experiences = originalC.fetchall()
        for experience in experiences:
            USER['experience'].append({
                'position': experience['job_title'],
                'organization': experience['company'],
                'start': experience['date_from'],
                'end': experience['date_to'],
                'phone': experience['telephone'],
                'email': experience['emailadd'],
                'duties': experience['duties'],
            })
        # pprint.pprint(USER)
        #print percentage completion
        print(f"{COUNT}/{TOTAL} :: {round(COUNT/TOTAL*100, 2)}% :: {ERRORED} errored", end='\r')
        # Migrate user
        COUNT += 1
        try:
            newC.execute("INSERT INTO users (name, username, role, email, nema, email_verified_at, password) VALUES (%s,%s,%s,%s,%s,%s,%s)", (USER['name'], USER['username'], USER['type'], USER['email'], USER['nema'] if USER['nema']!='"' else None, '2021-01-01', USER['password']))# Move the user to DB2
            id = newC.lastrowid
            if USER['certificate']['requested'] == 1:
                newC.execute("INSERT INTO certificates (user_id, number, expiry, verified) VALUES (%s,%s,%s,%s)", (id, f"EIK/{getNumber(USER['profile']['category'])}/{id}", '2025-1-1', '2024-5-22'))
            for file in USER['files']:
                newC.execute("INSERT INTO files (user_id, folder, title, name, url) VALUES (%s,%s,%s,%s,%s)", (id, 'requirements', file['title'], file['filename'], f"https://api.eik.co.ke/uploads/{id}/requirements/{file['filename']}"))
                # move(originPath + file['filename'], f"../public/uploads/{id}/requirements/{file['filename']}",id)
            if USER['type'] == 'Firm':
                newC.execute("INSERT INTO firms (user_id, kra, category, alternate, nationality, postal, town, county, phone, bio) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", (id, USER['profile']['kra'], USER['profile']['category'], USER['profile']['alternate'], USER['profile']['nationality'], USER['profile']['postal'], USER['profile']['town'], USER['profile']['county'], USER['profile']['phone'], USER['profile']['note']))
            else:
                newC.execute("INSERT INTO individuals (user_id, firm, category, alternate, nationality, nationalID, postal, town, county, phone, bio) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", (id, USER['profile']['firm'] if USER['profile']['firm'] != '"' else None, USER['profile']['category'], USER['profile']['alternate'], USER['profile']['nationality'], USER['profile']['nationalID'], USER['profile']['postal'], USER['profile']['town'], USER['profile']['county'], USER['profile']['phone'], USER['profile']['note']))
                for education in USER['education']:
                    newC.execute("INSERT INTO education (user_id, Title, Institution, Certification, start, end) VALUES (%s,%s,%s,%s,%s,%s)", (id, education['title'], education['institution'], education['certification'], education['start'], education['end']))
                for experience in USER['experience']:
                    newC.execute("INSERT INTO professions (user_id, Organization, Location, Position, Duties, Email, Phone, start, end) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)", (id, experience['organization'], 'Nairobi', experience['position'], experience['duties'], experience['email'], experience['phone'], experience['start'], experience['end']))
            new.commit()
            SUCCESS += 1
        except Exception as e:
            with open('logs.txt', 'a') as logs:
                logs.write(f"userID [{USER['id']}] failed to migrate error :: {e}\n")
            new.rollback()
            ERRORED += 1
    except Exception as e:
        with open('logs.txt', 'a') as logs:
            logs.write(f"userID [{USER['id']}] failed to migrate error :: {e}\n")
        new.rollback()
        ERRORED += 1

#print final stats total, %success, %failure
print(f"{COUNT}/{TOTAL} :: {round(SUCCESS/TOTAL*100, 2)}% success :: {round(ERRORED/TOTAL*100, 2)}% errored")

#cleanup
originalC.close()
newC.close()
original.close()
new.close()