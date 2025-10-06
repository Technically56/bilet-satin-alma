PRAGMA foreign_keys = ON;

CREATE TABLE Bus_Company(
    id UUID PRIMARY KEY,
    name TEXT UNIQUE NOT NULL,
    logo_path TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE User(
    id UUID PRIMARY KEY,
    full_name TEXT,
    email TEXT UNIQUE NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('user', 'company', 'admin')),
    password TEXT NOT NULL,
    company_id UUID,
    FOREIGN KEY(company_id) REFERENCES Bus_Company(id),
    balance INTEGER DEFAULT 800,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE Trips(
    id UUID PRIMARY KEY,
    company_id UUID NOT NULL,
    FOREIGN KEY (comapny_id) REFERENCES Bus_Company(id),
    destination_city TEXT NOT NULL,
    arrival_time DATETIME NOT NULL,
    departure_time DATETIME NOT NULL,
    departure_city TEXT NOT NULL,
    price INTEGER NOT NULL,
    capacity INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Tickets(
    id UUID PRIMARY KEY,
    trip_id UUID NOT NULL,
    FOREIGN KEY (trip_id) REFERENCES Trips(id),
    user_id UUID NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id),
    status TEXT NOT NULL DEFAULT 'active' CHECK (status in ('active','canceled','expired')),
    total_price INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Booked_Seats(
    id UUID PRIMARY KEY,
    ticket_id UUID NOT NULL,
    FOREIGN KEY (ticket_id) REFERENCES Tickets(id),
    seat_number INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Coupons(
    id UUID PRIMARY KEY,
    code TEXT NOT NULL,
    discount REAL NOT NULL,
    usage_limit INTEGER NOT NULL,
    expire_date DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE User_Coupons(
    id UUID PRIMARY KEY,
    coupon_id UUID NOT NULL,
    FOREIGN KEY (coupon_id) REFERENCES Coupons(id),
    user_id UUID NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);