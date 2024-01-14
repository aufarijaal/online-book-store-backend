# Online Bookstore Database Design

## Tables

### 1. Authors

| Field       | Type         | Description            |
| ----------- | ------------ | ---------------------- |
| author_id   | INT          | Primary key            |
| author_name | VARCHAR(100) | Name of the author     |
| birth_date  | DATE         | Author's date of birth |
| nationality | VARCHAR(50)  | Author's nationality   |

### 2. Genres

| Field      | Type        | Description       |
| ---------- | ----------- | ----------------- |
| genre_id   | INT         | Primary key       |
| genre_name | VARCHAR(50) | Name of the genre |

### 3. Books

| Field          | Type          | Description                                |
| -------------- | ------------- | ------------------------------------------ |
| book_id        | INT           | Primary key                                |
| title          | VARCHAR(200)  | Title of the book                          |
| author_id      | INT           | Foreign key referencing Authors(author_id) |
| genre_id       | INT           | Foreign key referencing Genres(genre_id)   |
| publish_date   | DATE          | Publication date of the book               |
| price          | DECIMAL(10,2) | Price of the book                          |
| stock_quantity | INT           | Quantity available in stock                |

### 4. Customers

| Field       | Type         | Description              |
| ----------- | ------------ | ------------------------ |
| customer_id | INT          | Primary key              |
| first_name  | VARCHAR(50)  | Customer's first name    |
| last_name   | VARCHAR(50)  | Customer's last name     |
| email       | VARCHAR(100) | Customer's email address |
| address     | VARCHAR(200) | Customer's address       |

### 5. Orders

| Field        | Type          | Description                                    |
| ------------ | ------------- | ---------------------------------------------- |
| order_id     | INT           | Primary key                                    |
| customer_id  | INT           | Foreign key referencing Customers(customer_id) |
| order_date   | DATE          | Date the order was placed                      |
| total_amount | DECIMAL(10,2) | Total amount of the order                      |

### 6. Order_Items

| Field         | Type          | Description                                |
| ------------- | ------------- | ------------------------------------------ |
| order_item_id | INT           | Primary key                                |
| order_id      | INT           | Foreign key referencing Orders(order_id)   |
| book_id       | INT           | Foreign key referencing Books(book_id)     |
| quantity      | INT           | Quantity of the book in the order          |
| item_price    | DECIMAL(10,2) | Price of the book at the time of the order |
