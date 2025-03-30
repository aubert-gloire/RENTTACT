-- Add profile_image column
ALTER TABLE users
ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL;

-- Add bio column
ALTER TABLE users
ADD COLUMN bio TEXT DEFAULT NULL;

-- Add address column
ALTER TABLE users
ADD COLUMN address VARCHAR(255) DEFAULT NULL;
