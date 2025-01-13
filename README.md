# Task Team

This repository contains the source code for a web application developed as part of the exam for the course "Web Systems and Data Structures." The project is designed to manage personal or team tasks, allowing users to create, assign, and track tasks, view deadlines, monitor progress, and generate reports on completed activities.
Project Overview

The application includes the following features:

    Task Creation and Assignment: Users can create tasks, assign them to team members, and define deadlines.
    Progress Tracking: The application allows users to monitor the progress of tasks, marking them as completed or ongoing.
    Deadline Visualization: A visual display of task deadlines ensures users stay on track.
    Activity Reports: Users can generate reports on completed tasks to evaluate overall progress.
    Database: All task data, user information, and progress tracking are stored in a database for persistence.

## Deployment

To deploy the application locally or in any Docker-compatible environment, simply use the provided docker-compose file. Here's how to get started:

Clone this repository to your local machine:

    git clone <repository_url>

Navigate into the project directory:

    cd <project_directory>

Run the following command to start the application using Docker Compose:

    docker-compose up --build

    After the build completes, the application should be accessible via your browser at the appropriate local address (e.g., http://localhost:8080).

## Technologies Used

    Backend:  PHP
    Frontend: TailwindCSS, chart.js
    Database: MariaDB
    Docker:   Containerization for easy deployment

## Preloaded Users to Test the application easily
   - This account is the owner of a Team with already configured activities (note: here you can access the Team management section)
     - Username: Ago
     - Password: vallePassword123!
   - This account has preconfigured activities in the private area and participates in Ago's team
     - Username: Pino
     - Password: PinoPassword1
   - This account participates in the aforementioned team
     - Username: Giacomo
     - Password: GiaPasswordComo
