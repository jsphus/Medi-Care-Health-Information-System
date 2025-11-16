# Cloudinary Profile Picture Integration - Setup Instructions

## SQL Migration

Run the following SQL command to add the `profile_picture_url` column to the `users` table:

```sql
ALTER TABLE users ADD COLUMN profile_picture_url VARCHAR(500);
```

Alternatively, you can run the migration file:
```bash
psql -U your_user -d your_database -f migrations/add_profile_picture_url.sql
```

## Cloudinary Account Setup

### Step 1: Create Cloudinary Account

1. Go to https://cloudinary.com
2. Click "Sign Up" to create a free account
3. Complete the registration process

### Step 2: Get Your Credentials

1. After logging in, go to your Dashboard
2. You'll see your account details:
   - **Cloud Name** (e.g., `your-cloud-name`)
   - **API Key** (e.g., `123456789012345`)
   - **API Secret** (e.g., `abcdefghijklmnopqrstuvwxyz123456`)

### Step 3: Configure Environment Variables

Add the following to your `.env` file in the project root:

```env
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

Replace:
- `your_cloud_name` with your Cloudinary Cloud Name
- `your_api_key` with your Cloudinary API Key
- `your_api_secret` with your Cloudinary API Secret

### Step 4: Verify Configuration

1. Make sure your `.env` file is in the project root directory
2. Ensure the file is not committed to version control (should be in `.gitignore`)
3. Restart your web server if needed

## Features

### Profile Picture Upload
- Users can upload profile pictures through their account settings
- Supported formats: JPG, JPEG, PNG, GIF, WEBP
- Maximum file size: 5MB
- Images are automatically optimized and stored on Cloudinary
- Old images are automatically deleted when a new one is uploaded

### Profile Picture Display
- Profile pictures appear in:
  - User settings pages (all roles)
  - User list in Super Admin panel
- Falls back to letter avatars if no profile picture is set
- Images are displayed in circular format

### Profile Picture Management
- Upload new profile picture
- Remove existing profile picture
- Preview before uploading

## Usage

### For Users

1. Navigate to Settings (accessible from your account menu)
2. Scroll to the "Profile Picture" section
3. Click "Choose File" and select an image
4. Click "Upload" to save your profile picture
5. Click "Remove" to delete your current profile picture

### For Administrators

Profile pictures are automatically displayed in the user management interface. No additional configuration needed.

## Technical Details

- **Storage**: Images are stored on Cloudinary CDN
- **Folder Structure**: Images are stored in `profile_pictures/` folder
- **Naming Convention**: `profile_pictures/user_{user_id}_{timestamp}`
- **Database**: Profile picture URLs are stored in `users.profile_picture_url` column
- **API**: Uses Cloudinary REST API with signed uploads for security

## Troubleshooting

### Upload Fails
- Check that Cloudinary credentials are correctly set in `.env`
- Verify file size is under 5MB
- Ensure file format is supported (JPG, PNG, GIF, WEBP)
- Check PHP error logs for detailed error messages

### Image Not Displaying
- Verify the URL is correctly stored in the database
- Check that Cloudinary account is active
- Ensure image hasn't been deleted from Cloudinary

### Signature Errors
- Verify API Secret is correct
- Check that all required parameters are included
- Ensure timestamp is current (not expired)

## Security Notes

- API Secret should never be exposed in client-side code
- All uploads are authenticated using signed requests
- Old images are automatically cleaned up when replaced
- File type and size validation is performed server-side

