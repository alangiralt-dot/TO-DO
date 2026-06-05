# 👨‍💻 TO-DO User Cases
## 🚀 Use Cases Over-view:
This section documents the functional behaviour of the TO-DO application from the user's perspective. The application is built on a custom PHP MVC template (no framework), where each user interaction is routed through a front controller to the appropriate controller action, which in turn coordinates with the model layer to persist or retrieve data from the database.
The use cases described here cover the core functionality of the application, which follows the classic CRUD pattern (Create, Read, Update, Delete) applied to the central entity of the system: the task. Specifically, the documented use cases address:

0. Open app — accessing to the app, and describing specifities of the landing page.
1. Add task — adding a new task to the system.
5. List one or more task based on a specific criteria — retrieving and displaying the details of one or more task identified by its attributes (createdBy, start time, end time or status).
4. List all tasks — retrieving and displaying the complete collection of tasks.
2. Update task — modifying the data of an existing task.
3. Delete task — removing a task from the system.

## 📱Open APP:
This scenario describes the user procedure and requriements to initialize the app.

### Actor: Generic User
### Goal: Open for a first time the app TO-DO.
### Pre-conditions: Access to internet through a browser.
### Scenarios:
- Main success scenario:
    1. User inputs URL in the search bar.
    2. The browser sends a request to the server.
    3. Server builds and respond to the user landing page using the `blankView` object.

@startuml
title Open App - Main Success Scenario

actor User
participant Browser
participant Server

User -> Browser : Input URL in the search bar
Browser -> Server : Send HTTP request
Server -> Server : Build landing page (firstView object)
Server --> Browser : Respond with landing page
Browser --> User : Display landing page

@enduml

## 🔨 Add Task
This scenario describes the user procedure to add a task in the app TO-DO.

### Actor: Generic User
### Goal: Generate a new task for an user in the system.
### Pre-conditions: Open App.
### Scenarios:
- Main success scenario:
    1. User click on the button `addTask` in either `blankView` or `taskView`.
    2. The browser sends a request to the server.
    3. The server responds with the `formView` rendered.
    4. The user inputs the required fields (description, created by, status, start time, end time). `status` and `start_time` are pre-compiled by the server, and can be modified by the user.
    5. The user submits the request by clicking `add`.
    6. The server process the request, and writes a new record in the persistence file.
    7. The server renders and responds with a `taskView` with the task record just created, no other tasks are displayed.

@startuml
title Add Task - Main Success Scenario

actor User
participant Browser
participant Server
database "Persistence File" as Persistence

User -> Browser : Click "addTask" (from blankView or taskView)
Browser -> Server : Request add-task form
Server --> Browser : Respond with formView rendered
Browser --> User : Display form (status, start_time pre-filled)

User -> Browser : Input fields (description, created by,\nstatus, start_time, end_time)
User -> Browser : Click "add" (submit)
Browser -> Server : Submit form data

Server -> Persistence : Write new task record
Persistence --> Server : Confirm write

Server -> Server : Render taskView with created task
Server --> Browser : Respond with taskView
Browser --> User : Display taskView with new task

@enduml

- Alternative flow - users cancels add task:
    1. At step 5: the user cancel the request by clicking on `cancel`.
    2. The server responds with the `blankView`.


## ✅ List one or more task based on a specific criteria
This scenario describes the user procedure to retreive one or more task, based on a condition that can use multiple parameters.

### Actor: Generic User
### Goal: Visualize one or more task that matches the specific attributes input by the user.
### Pre-conditions: Open App, Add Task(the database contains data), the table on display could either contain data or not.
### Scenarios:
- Main success scenario:
    1. The user inputs the criteria she/he wants to use to filter the database, using the fields on the `filter bar` of the `TaskView` or the `BlankView`: status,  from (start time), to (end time), key words (description), createdBy.
    2. The user click on the `search icon` 🔍.
    3. The browser sends the request based on the parameter(fields) compiled by the user in the `filter bar`.
    4. The server validates the request, and ignores the empty parameters.
    5. The server interrogates the persistance model to find the tasks that match all the criteria input by the user.
    6. The server renders the `TaskView` with the task table containing all the tasks matching the criteria, as a response to the user. The filter bars has all the fields empty on the `TaskView` always.


## ✅ List all tasks
This scenario describes the user procedure to retreive all the tasks.

### Actor: Generic User
### Goal: Visualize one or more task that matches the specific attributes input by the user.
### Pre-conditions: Open App, Add Task(the database contains data), the `task table`could either contain data or not.
### Scenarios:
- Main success scenario:
    1. The user inputs the criteria she/he wants to use to filter the database, using the fields on the `filter bar` of the `TaskView` or the `BlankView`: status,  from (start time), to (end time), key words (description), createdBy.
    2. The user click on the `search icon` 🔍.
    3. The browser generates the request based on the parameter(fields) compiled by the user.
    4. The server process the request and interrogates the persistance model to find the tasks that match all the criteria input by the user.
    5. The server renders the `TaskView` with the `task table` containg all the task matching the criteria, and responds to the user.


## ✏️ Update Task
This scenario describes the user procedure to modify an existing task.

### Actor: Generic User
### Goal: Modify on or more attributes of an existing task record in the `task table`.
### Pre-conditions: Open App, Add Task(the database contains data), the `task table`contains data.
### Scenarios:
- Main success scenario:
    1. The user clicks on the `updateTask icon`✏️, in a specific row of the table.
    2. The browser sends a request to the server with the `id` of the task.
    3. The server interrogates the persistance model with the `id` to retreive the information for task selected by the user.
    4. The server responds with the `formView`, where every field (description, created by, status, start time, and end time) is pre-populated.
    5. The user modifies the field or fields, she/he intends to modify.
    6. The user submits the request by clicking `save`.
    7. The server process the request, and updates the existing record in the database.
    8. The server renders and responds with a `taskView` with the task record that was modified, no other tasks are displayed.

## 🗑️ Delete Task
This scenario describes the user procedure to delete an existing task.

### Actor: Generic User
### Goal: Delete an existing task record in the `task table`.
### Pre-conditions: Open App, Add Task(the database contains data), the `task table`contains data.
### Scenarios:
- Main success scenario:
    1. The user clicks on the `deleteTask icon` 🗑️ , in a specific row of the table.
    2. An element demands the user to confirm the action.
    3. If the user accepts the warning, the browser sends a request to the server with the `id` of the task.
    4. The server deletes the reccord of the corresponding task in the database.
    5. The server renders and responds with a `BlankView` with an element containing the server message confirming the deletion of the task.

# Option features:

- Order task by criteria (optional).
- Use JavaScript to modify a row within the table view.
- Chached filter parameters in a session variable to return a default view.