# 👨‍💻 TO-DO User Cases
## 🚀 Use Cases Over-view:
This section documents the functional behaviour of the TO-DO application from the user's perspective. The application is built on a custom PHP MVC template (no framework), where each user interaction is routed through a front controller to the appropriate controller action, which in turn coordinates with the model layer to persist or retrieve data from the database.
The use cases described here cover the core functionality of the application, which follows the classic CRUD pattern (Create, Read, Update, Delete) applied to the central entity of the system: the task. Specifically, the documented use cases address:

0. Open app — accessing to the app, and describing specifities of the landing page.
1. Create task — adding a new task to the system.
2. Update task — modifying the data of an existing task.
3. Delete task — removing a task from the system.
4. List all tasks — retrieving and displaying the complete collection of tasks.
0. List a specific task — retrieving and displaying the details of a single task identified by its ID.

## 📱Open APP:
This scenario describes the user procedure and requriements to initialize the app.

### Actor: Generic User
### Goal: Open for a first time the app TO-DO.
### Pre-conditions: Access to internet through a browser.
### Scenarios:
- Main success scenario:
    1. User inputs URL in the search bar.
    2. The browser sends a request to the server.
    3. Server builds and respond to the user landing page using the `firstView` object.

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