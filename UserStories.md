# User Stories — Municipality Issue System

---

## Citizen

---

**Registering an account**

The citizen navigates to the registration page. They enter their full name, email address, and a password, then confirm the password. If any field is missing or the passwords do not match, the system highlights the error and asks them to correct it. Once all fields are valid and the form is submitted, the system creates the account and redirects the citizen to their dashboard.

---

**Logging in**

The citizen opens the login page and enters their email address and password. If the credentials are incorrect, the system displays an error message and the citizen remains on the login page. If the credentials are correct, the system authenticates the citizen and redirects them to their dashboard. If the citizen has forgotten their password, they click 'Forgot password?', enter their email address, and receive a reset link by email. They follow the link, enter a new password, confirm it, and the system updates their credentials.

---

**Submitting a complaint**

The citizen navigates to the submit complaint page. They enter a title and a description of the issue, select a category from the available list, and enter the location where the issue was observed. They may optionally attach a photo of the issue. If the image format is not supported or exceeds the size limit, the system informs the citizen and asks them to upload a different file. If a complaint with the same title and location has already been submitted, the system warns the citizen of a potential duplicate before they proceed. Once the form is valid and submitted, the system records the complaint with a status of 'Submitted' and confirms the submission to the citizen.

---

**Viewing complaints**

The citizen navigates to the My Complaints page. The system displays a list of all complaints the citizen has submitted, showing the title, category, current status, and submission date for each one. The citizen clicks on any complaint to open its detail page. On the detail page, they can see the full description, the attached photo if one was provided, and a timeline of every status change the complaint has gone through, including any comments left by staff.

---

**Receiving and managing notifications**

Each time a staff member updates the status of one of the citizen's complaints, the system creates a notification for that citizen. An unread count badge appears on the notification bell in the navigation bar. The citizen clicks the bell to see a dropdown of their most recent notifications. They can click on a notification to be taken directly to the relevant complaint, at which point the notification is marked as read. If there are many notifications, the citizen selects 'View all notifications' to see the full list. They may also mark all notifications as read at once from the dropdown.

---

**Updating their profile**

The citizen navigates to their profile page. They can update their display name or email address by editing the relevant field and saving the form. They can also change their password by entering their current password followed by the new password and its confirmation. If the current password is incorrect or the new passwords do not match, the system displays an error. Upon a successful save, the system confirms the update.

---

## Staff

---

**Reviewing the complaints list**

The staff member logs in and is taken to the staff dashboard. They navigate to the Complaints page, where the system displays all complaints submitted across all citizens. The list shows each complaint's title, category, current status, and submission date. The staff member can filter the list by status or category to narrow down the complaints that require attention.

---

**Validating a complaint**

The staff member opens a complaint that is in the 'Submitted' or 'Pending Review' status. They review the title, description, location, and any attached photo. If the complaint is legitimate and contains sufficient information, the staff member selects 'Validate'. The system updates the status to 'Validated', records a status history entry, and sends a notification to the citizen who submitted the complaint.

---

**Rejecting a complaint**

The staff member opens a complaint and determines that it does not meet the required criteria. They select 'Reject' and are required to enter a rejection reason before confirming. The system updates the status to 'Rejected', records the reason in the status history, and sends a notification to the citizen informing them that their complaint was rejected and why.

---

**Progressing a complaint**

Once a complaint has been validated, the staff member can continue to update its status as work progresses. They open the complaint and select the next appropriate status — either 'In Progress', 'Resolved', or 'Closed'. They may optionally add a comment to provide further context. The system records the change in the status history and notifies the citizen of the update. If the staff member attempts to skip a status or move to an invalid transition, the system prevents the action.

---

## Admin

---

**Monitoring the dashboard**

The admin logs in and is taken to the admin dashboard. The system displays a summary of key statistics: the total number of complaints, a breakdown by status, and a breakdown by category. This gives the admin an at-a-glance view of the current state of all reported issues in the system.

---

**Managing users**

The admin navigates to the Users page, which lists all registered accounts along with their name, email, role, and active status. The admin can create a new user by entering their name, email, password, and assigning a role of 'citizen', 'staff', or 'admin'. If the email address is already in use, the system rejects the submission and displays an error. The admin can also deactivate an existing account, which prevents that user from logging in without deleting their data.

---

**Managing categories**

The admin navigates to the Categories page, which lists all existing complaint categories. They can add a new category by entering a name. If a category with the same name already exists, the system rejects the submission. The admin can also edit the name of an existing category or delete one that is no longer needed. If a category has complaints associated with it, the system warns the admin before allowing deletion.

---

## Security & Access Control

---

**Role-based access**

When any user attempts to access a page, the system checks their assigned role against the route they are requesting. A citizen who attempts to access a staff or admin route is shown a 403 Forbidden page. A staff member who attempts to access an admin route is similarly blocked. Only users with the correct role may view or perform actions on the pages assigned to that role.

---

**Logging out**

Any authenticated user can log out by selecting 'Sign Out' from the navigation menu. The system ends the session immediately and redirects the user to the login page. If the user attempts to navigate back to a protected page after logging out, the system redirects them to the login page.
