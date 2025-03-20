# snipsnap
Register: POST http://localhost:8000/api/auth/register
Login: POST http://localhost:8000/api/auth/login
Once authenticated, you can use the snippet endpoints:
List snippets: GET http://localhost:8000/api/snippets
Create snippet: POST http://localhost:8000/api/snippets
Get snippet: GET http://localhost:8000/api/snippets/{id}
Update snippet: PUT http://localhost:8000/api/snippets/{id}
Delete snippet: DELETE http://localhost:8000/api/snippets/{id}
Toggle favorite: POST http://localhost:8000/api/snippets/{id}/favorite
Tag endpoints:
List tags: GET http://localhost:8000/api/tags
Create tag: POST http://localhost:8000/api/tags
Get tag: GET http://localhost:8000/api/tags/{id}
Update tag: PUT http://localhost:8000/api/tags/{id}
Delete tag: DELETE http://localhost:8000/api/tags/{id}