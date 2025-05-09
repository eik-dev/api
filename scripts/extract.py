import mysql.connector
import csv
import os

# Define all TWG names
all = [
    'Environmental Educators',
    'Watershed Catchment Management (Blue economy)',
    'Sustainable Waste Management',
    'Climate Science',
    'Biodiversity / Natural Sciences',
    'Built Environment & Construction',
    'Clean Energy and Renewables',
    'Environmental Policy & Governance',
    'Environmental Advocacy - Youth & Women Groups'
]

new = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

newC = new.cursor(dictionary=True, buffered=True)

# Create output directory if it doesn't exist
os.makedirs("twg_output", exist_ok=True)

# Initialize a dictionary to store users for each TWG
twg_data = {twg_name: [] for twg_name in all}

# Get all users with their TWGs
newC.execute("SELECT * FROM t_w_g_s")
twgs = newC.fetchall()

for twg in twgs:
    # Use parameterized query to prevent SQL injection
    newC.execute("SELECT name, email, number FROM users WHERE id = %s", (twg['user_id'],))
    user = newC.fetchone()
    if not user:
        continue
    
    # Parse the TWGs string - assuming it's a comma-separated string of TWG names
    try:
        # Remove brackets and split by comma
        user_twgs = twg['twgs'].strip('[]').split(',')
        # Clean up each TWG name (remove quotes, trim whitespace)
        user_twgs = [t.strip().strip('"\'') for t in user_twgs]
        
        # Add user to each TWG they belong to
        for twg_name in user_twgs:
            if twg_name in all:
                twg_data[twg_name].append({
                    'name': user['name'],
                    'email': user['email'],
                    'number': user['number']
                })
    except Exception as e:
        print(f"Error processing TWGs for user {twg['user_id']}: {e}")

# Create a CSV file for each TWG in the 'all' list
for twg_name in all:
    # Create a safe filename from the TWG name
    safe_name = twg_name.replace('/', '_').replace('\\', '_').replace(' ', '_')
    filename = f"twg_output/{safe_name}.csv"
    
    with open(filename, 'w', newline='') as csvfile:
        fieldnames = ['name', 'email', 'number']
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        
        writer.writeheader()
        for user in twg_data[twg_name]:
            writer.writerow(user)

# Close the database connection
newC.close()
new.close()