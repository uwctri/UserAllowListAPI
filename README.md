# User AllowList API - Redcap External Module

## What does it do?

Exposes an API, which requires a super API token, that can be used to add, remove, or search for user names in the Redcap allowlist. This is useful for organizations that want to automate an access managment workflow managed by an external system.

## Requests

```sh
POST /api/?NOAUTH&type=module&prefix=user_allowlist_api&page=api

body {
    "token": "string"  // Superuser token
    "action": "string" // 'add', 'remove', 'search'
    "user": "username" 
}

```

## Responses

```sh
{
    "status": "string",  // 'success' or 'failure'
    "message": "string", // Explination of action that occured
    "value": bool        // ture or false. Null on error
}
```

## Installing

You can install the module from the REDCap EM repo or drop it directly in your modules folder (i.e. `redcap/modules/user_allowlist_api_v1.0.0`).
