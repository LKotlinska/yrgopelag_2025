# Terracotta Bay Spa & Hotel - Yrgopelag

This repository contains a web project developed as part of a school assignment.  
The project implements a simple booking-style application with a clear file structure and basic backend functionality.

---

## Purpose

The purpose of this project is to apply fundamental web development concepts in a structured and readable way.  
Focus has been placed on separating logic from presentation and organizing files in a way that supports further development.

---

## Content & Structure

The application is built with a straightforward content structure:

- Headings are used to create a clear hierarchy
- Text content is short and neutral in tone
- Navigation and layout follow consistent patterns

The written content is intentionally kept simple to prioritize clarity and usability.

---

## Project Organization

The project is divided into distinct parts with separate responsibilities:

- **Views**  
  Files responsible for rendering pages and layouts.

- **Components**  
  Reusable interface elements shared across multiple pages.

- **Controllers**  
  Server-side scripts that handle form submissions and application logic.

- **Functions**  
  Helper functions used for validation, calculations, and repeated logic.

This organization reduces duplication and improves readability.

---

## Reuse of Code

To avoid repeated code:

- Shared elements are implemented as components
- Common logic is extracted into functions
- Messages for errors and success states use a shared structure

This makes the application easier to maintain and modify.

---

## User Feedback & Validation

User input is validated before processing.  
Feedback is provided when validation fails or when an action is completed successfully.  
Messages are written to be understandable without technical knowledge.

---

## Technologies

- HTML
- PHP
- JavaScript
- SQLite / MySQL

No external frameworks are used.

---

## What the Project Covers

- Basic application flow from input to response
- Server-side handling of form data
- Database interaction
- Reusable components
- Simple client-side interaction

---

## Future Improvements

Given additional development time, the following improvements would be implemented:

- **Discount handling**  
  Information about whether a guest is a returning customer could be stored as part of the booking or guest data. This would allow the receipt page to fetch and display applied discounts.

- **Admin panel structure**  
  The admin panel could be improved by allowing full-form submission instead of updating one row at a time. On submission, the backend would update only the fields that contain input, so that multiple changes to be applied in a single request.

- **Admin component reuse**  
  Creating reusable components for the admin interface (for example: form rows, input groups, and action buttons) would improve readability and reduce duplication across admin views.

- **Admin backend separation**  
  Backend logic related to administrative actions could be further separated into dedicated functions or modules. This would improve maintainability and make the admin functionality easier to extend or modify independently from the public-facing application.

---

## Additional Information

This project was developed for educational purposes.

---

## Author

Laura Greta Kotlinska
Web development student at Yrgo

---
