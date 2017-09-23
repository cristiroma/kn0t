# drupal.boilerplate.8
Inspired by drupal-composer/drupal-project, tailored to my needs

# Starting a new project

```
composer -vv create-project cristiroma/drupal-boilerplate-8 website --stability dev --no-interaction
```


# Creating a punch time record using curl

```
curl -X POST -H "Content-type: application/json" --user username:password http://drupal.website/entity/staff_time_entry --data-binary "{}"
```
