-- Migration: Add profile_picture_url column to users table
-- Date: 2024
-- Description: Adds a column to store Cloudinary profile picture URLs for users

ALTER TABLE users ADD COLUMN profile_picture_url VARCHAR(500);

