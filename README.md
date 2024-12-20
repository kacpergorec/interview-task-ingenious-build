[See original project specifications here.](ORIGINAL_README.md)

> [!NOTE]
> Thanks to everyone involved in the recruitment process! I really appreciate your time and the opportunity.


## Project Overview
- **PHP 8.3**, 
- **Laravel**, 
- **PHPUnit**,
- **Docker**,
- **SQLite**

## Goal
A simple app to **save**, **retrieve**, and **send invoices**, with a emphasis on DDD, Events and Unit Tests.


## Thought Process
### Repositories
I try to keep a framework-agnostic approach, but my years of experience with Symfony have made the Repository Pattern feel comfortable, so I ended up enforcing it. I separated the repositories into query repositories and regular repositories.

- [QueryRepository.php]() 
- [Repository.php]()

The retrieved model is directly mapped to a Domain entity. Looking back, I think it would have been cleaner to map it to an Infrastructural DTO first and then pass that to the Application service.

### Money ValueObject
I added the Money ValueObject to future-proof the maintainability of the project, especially if it heads toward international sales. I think it's a good practice to include it when the project is moving in that direction.

### UUID Library Change
I switched from **Ramsey UUID** to **Symfony UUIDs** because I realized a bit too late that the project already used an exsiting UUID library. Refactoring provided code to Symfony UUIDs was quicker.

### Domain Validation
Domain validation is **bound to the entity** to ensure business rules are enforced consistently.

### JSON Errors
One change I would make, if I had more time, is to add middleware (or use try-catch in controllers) to format exceptions into readable JSON.

### Customer
Customer is a **ValueObject**, though it could be an **Entity** if it had a more complex lifecycle.

### Resending Failed Invoices
I added a **commented-out example** for resending failed invoices. While this wasn’t part of the original requirements, I think it’s a crucial feature for a real-world applicationŌ.


## Project Challenges
### Finals
I didn’t use `final` in this project that much due to time constraints and challenges with mocking. Given more time, I could have implemented `final` on certain services and mocked them through interfaces. 

### Folder Structure
I’m more accustomed to working with **3 folders**: **Application**, **Domain**, and **Infrastructure**. The separation into **5 folders** for **API** and **Presentation** was a bit unfamiliar, though I believe I understand the intended layer separation.

### Exceptions
Looking back, I could have implemented **domain-specific exceptions** as well as general exceptions for other layers.


## Conclusion
I enjoyed working on this project and I hope it showcases my skills. I appreciate the opportunity and I look forward to hearing your feedback. Thank you for your time and consideration.
