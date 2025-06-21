# API Internationalization (i18n) Documentation

## Overview

The SAIF Backend API supports internationalization through the `Accept-Language` HTTP header. The API automatically returns content in the requested language if available, falling back to English (`en`) as the default.

## Supported Languages

- **English** (`en`) - Default
- **Arabic** (`ar`)

## How It Works

The API uses a middleware (`SetLocaleFromHeader`) that:

1. Reads the `Accept-Language` header from incoming requests
2. Parses the header to determine preferred languages
3. Sets the application locale based on the first supported language found
4. Falls back to English if no supported language is found

## Usage Examples

### Basic Usage

Request content in English:
```bash
curl -H "Accept-Language: en" https://api.example.com/api/packages
```

Request content in Arabic:
```bash
curl -H "Accept-Language: ar" https://api.example.com/api/packages
```

### Without Accept-Language Header

If no `Accept-Language` header is provided, the API defaults to English:
```bash
curl https://api.example.com/api/packages
```

### Complex Accept-Language Headers

The API supports standard HTTP Accept-Language syntax with quality values:

```bash
# Prefer Arabic, but accept English as second choice
curl -H "Accept-Language: ar;q=0.9,en;q=0.8" https://api.example.com/api/packages

# Prefer English, but accept Arabic as second choice
curl -H "Accept-Language: en;q=0.9,ar;q=0.8" https://api.example.com/api/packages
```

### Regional Language Codes

The API also supports regional language codes:

```bash
# US English
curl -H "Accept-Language: en-US" https://api.example.com/api/packages

# Saudi Arabian Arabic
curl -H "Accept-Language: ar-SA" https://api.example.com/api/packages
```

## API Endpoints Supporting i18n

All API endpoints that return translatable content support i18n:

- `/api/packages` - List all packages
- `/api/packages/{slug}` - Get a specific package
- `/api/retreats` - List all retreats
- `/api/retreats/{id}` - Get a specific retreat
- `/api/destinations` - List all destinations
- `/api/destinations/{slug}` - Get a specific destination
- `/api/faqs` - List all FAQs

## Response Examples

### English Response
```json
{
  "data": {
    "type": "packages",
    "id": 1,
    "attributes": {
      "name": "Wellness Retreat Package",
      "description": "Experience ultimate relaxation and rejuvenation",
      "goal": "Achieve complete wellness and stress relief"
    }
  }
}
```

### Arabic Response
```json
{
  "data": {
    "type": "packages",
    "id": 1,
    "attributes": {
      "name": "باقة الخلوة الصحية",
      "description": "اختبر الاسترخاء التام والتجديد",
      "goal": "تحقيق العافية الكاملة والتخلص من التوتر"
    }
  }
}
```

## Implementation Details

### Middleware

The `SetLocaleFromHeader` middleware is automatically applied to all API routes. It:

- Parses the `Accept-Language` header
- Supports multiple languages with quality values
- Handles regional language codes (e.g., `en-US` → `en`)
- Sets the Laravel application locale accordingly

### Translatable Fields

Models using the `Spatie\Translatable\HasTranslations` trait automatically return content in the current locale. Common translatable fields include:

- Package: `name`, `description`, `goal`, `program`, `activities`, `stay`, `iv_drips`
- Retreat: `name`
- Destination: `name`
- FAQ: `question`, `answer`

## Client Implementation Examples

### JavaScript (Fetch API)
```javascript
// Request Arabic content
fetch('https://api.example.com/api/packages', {
  headers: {
    'Accept-Language': 'ar'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### JavaScript (Axios)
```javascript
// Set default language for all requests
axios.defaults.headers.common['Accept-Language'] = 'ar';

// Or for a specific request
axios.get('https://api.example.com/api/packages', {
  headers: {
    'Accept-Language': 'ar'
  }
});
```

### Python
```python
import requests

# Request Arabic content
response = requests.get(
    'https://api.example.com/api/packages',
    headers={'Accept-Language': 'ar'}
)
data = response.json()
```

### cURL
```bash
curl -X GET "https://api.example.com/api/packages" \
     -H "Accept-Language: ar" \
     -H "Accept: application/json"
```

## Best Practices

1. **Always include Accept-Language header** - While the API defaults to English, explicitly setting the header ensures consistent behavior.

2. **Use language codes from supported list** - Stick to `en` or `ar` to ensure content is available.

3. **Handle missing translations gracefully** - Some content might not be translated yet. The API will return the fallback language (English) in such cases.

4. **Cache by language** - If implementing client-side caching, ensure cache keys include the language to prevent serving wrong language content.

5. **User preference storage** - Store user's language preference and automatically include it in API requests.

## Testing

To test the i18n functionality:

```bash
# Test English
curl -H "Accept-Language: en" http://localhost:8000/api/packages

# Test Arabic
curl -H "Accept-Language: ar" http://localhost:8000/api/packages

# Test fallback (should return English)
curl -H "Accept-Language: fr" http://localhost:8000/api/packages
```

## Troubleshooting

1. **Content not in expected language**
   - Verify the `Accept-Language` header is being sent correctly
   - Check if the content has been translated for that field
   - Ensure the language code is supported (`en` or `ar`)

2. **Getting English content when requesting Arabic**
   - The specific field might not have an Arabic translation yet
   - The model might not have the field marked as translatable

3. **Performance considerations**
   - The i18n system has minimal performance impact
   - Translations are stored in JSON columns in the database
   - No additional queries are needed for translations