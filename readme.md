# Slack

- A slack API integeration web application.
- I've added an application token for API calls in .env.
- Create a slack workspace
- visit https://api.slack.com/ and it will ask you to sign in with your slack account
- Create a new app, it'll ask you to sign in to a workspace
- Go to your apps, select the app you just created.
- Add permissions to the application` ['channels.write','channels.read' ]`.
- Select install your apps to your workspace then authorize.
- Go to OAuth & Permissions on the left panel then copy the token and paste it in `.env` file `SLACK_TOKEN = token`

## Installation

- `composer install`

- `php artisan key:generate`

- `cp .env.example .env`

- `php artisan migrate`

- `php artisan serve`

## Features
-  Create channel
- invitate use to a channel