# Blog API

A simlpe blog api allowing users to preform basic CRUD operations on a blog post.
Users can add images to the blog post which are stored on Google Cloud Storage.

## Running the application

### Locally

Make sure mysql is running with a database named "blog".

```shell
php artisan migrate
php -S localhost:8080 -t public
```
