# My Data Plugin

A WordPress plugin for managing member data with CSV import functionality.

## Features

- **Member Management**: View all members in a clean, organized table
- **CSV Import**: Import member data from CSV files
- **Auto Database Setup**: Automatically creates the required database table on plugin activation
- **WordPress Security**: Uses WordPress nonces for form security
- **Responsive Design**: Clean, modern interface with built-in styling

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'New data' in the WordPress admin menu

## CSV Format

The CSV file should contain the following columns in order:

- **Name**: Member's full name
- **Email**: Member's email address (must be unique)
- **Phone**: Member's phone number
- **Status**: 1 for Active, 0 for Inactive

### Example CSV:

```
Name,Email,Phone,Status
John Doe,john@example.com,123-456-7890,1
Jane Smith,jane@example.com,098-765-4321,0
```

## Usage

1. Go to the **New data** menu in WordPress admin
2. Click the **Import CSV** button to show the upload form
3. Select your CSV file and click **Import CSV**
4. View the imported data in the table below

## Database Table

The plugin creates a table named `wp_members` (prefixed with your WordPress table prefix) with the following structure:

- `id` - Auto-incrementing primary key
- `name` - Member name
- `email` - Member email (unique)
- `phone` - Member phone number
- `status` - Active (1) or Inactive (0)
- `created` - Timestamp when record was created
- `modified` - Timestamp when record was last updated

## Security Features

- WordPress nonce verification for form submissions
- Data sanitization using WordPress functions
- Prepared SQL statements to prevent injection attacks
- Proper escaping of output data

## References

- [How to import CSV data into WordPress custom fields](https://dev.to/matteodefilippis/how-to-import-csv-data-into-wordpress-custom-fields-m2m)
- [YouTube Tutorial](https://youtu.be/NSjI3LuVUqU?si=x_XrHu3y769uSo9h)
- [Import CSV File Data into MySQL Database PHP](https://www.codexworld.com/import-csv-file-data-into-mysql-database-php/)

## Changelog

### Version 1.0

- Initial release
- Member data management
- CSV import functionality
- WordPress security integration
- Auto database table creation
