<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Snippet;
use App\Models\Tag;

class SnippetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the test user (or first user in the database)
        $user = User::where('email', 'test@example.com')->first() ?? User::first();

        if (!$user) {
            // Create a test user if none exists
            $user = User::create([
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => bcrypt('password')
            ]);

            $this->command->info('Created test user since none existed.');
        }

        // Define common programming languages
        $languages = [
            'JavaScript', 'Python', 'PHP', 'Java', 'C#',
            'C++', 'Ruby', 'Go', 'TypeScript', 'Swift',
            'Kotlin', 'Rust', 'SQL', 'HTML', 'CSS'
        ];

        // Create 50 snippets with various content
        $count = 1;
        $snippetsData = $this->getSnippetData();
        $createdCount = 0;

        foreach ($snippetsData as $snippetData) {
            // Set random language if not specified
            if (!isset($snippetData['language'])) {
                $snippetData['language'] = $languages[array_rand($languages)];
            }

            // Set user ID
            $snippetData['user_id'] = $user->id;

            // Set favorite status (30% chance of being favorite)
            $snippetData['is_favorite'] = rand(0, 9) < 3;

            // Extract tags before creating snippet
            $tags = isset($snippetData['tags']) ? $snippetData['tags'] : $this->generateTags($snippetData['language']);

            // Remove tags from snippet data as it's not a column in the snippets table
            if (isset($snippetData['tags'])) {
                unset($snippetData['tags']);
            }

            // Create the snippet
            $snippet = Snippet::create($snippetData);

            // Create tags for the snippet
            foreach ($tags as $tagName) {
                // Create tag if it doesn't exist
                $tag = Tag::firstOrCreate(
                    ['name' => strtolower($tagName), 'user_id' => $user->id]
                );

                // Attach tag to snippet
                $snippet->tags()->attach($tag->id);
            }

            $createdCount++;

            if ($createdCount >= 50) {
                break;
            }
        }

        $this->command->info("Created {$createdCount} snippets with tags.");
    }

    /**
     * Generate random tags based on language and common programming concepts
     */
    private function generateTags($language)
    {
        $commonTags = ['algorithm', 'function', 'example', 'tutorial', 'utility', 'snippet'];

        // Language-specific tags
        $langTag = strtolower($language);
        $tags = [$langTag];

        // Add 2-3 random common tags
        shuffle($commonTags);
        $tagsCount = rand(2, 3);

        for ($i = 0; $i < $tagsCount; $i++) {
            $tags[] = $commonTags[$i];
        }

        return $tags;
    }

    /**
     * Get predefined snippet data
     */
    private function getSnippetData()
    {
        return [
            // JavaScript Snippets
            [
                'title' => 'JavaScript Array Filter',
                'description' => 'Filter an array to get only even numbers',
                'language' => 'JavaScript',
                'code' => "// Filter an array to get only even numbers\nconst numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\nconst evenNumbers = numbers.filter(num => num % 2 === 0);\nconsole.log(evenNumbers); // [2, 4, 6, 8, 10]",
                'tags' => ['javascript', 'array', 'filter', 'functional']
            ],
            [
                'title' => 'JavaScript Promise Example',
                'description' => 'Basic example of using promises in JavaScript',
                'language' => 'JavaScript',
                'code' => "// Basic Promise example\nfunction fetchData() {\n  return new Promise((resolve, reject) => {\n    setTimeout(() => {\n      const data = { id: 1, name: 'Example Data' };\n      resolve(data);\n      // For error: reject(new Error('Failed to fetch'));\n    }, 1000);\n  });\n}\n\nfetchData()\n  .then(data => console.log('Success:', data))\n  .catch(error => console.error('Error:', error));",
                'tags' => ['javascript', 'promise', 'async', 'es6']
            ],
            [
                'title' => 'JavaScript Map Function',
                'description' => 'Transform array elements using map',
                'language' => 'JavaScript',
                'code' => "// Transform array with map\nconst numbers = [1, 2, 3, 4, 5];\nconst squared = numbers.map(x => x * x);\nconsole.log(squared); // [1, 4, 9, 16, 25]",
                'tags' => ['javascript', 'array', 'map', 'functional']
            ],

            // Python Snippets
            [
                'title' => 'Python List Comprehension',
                'description' => 'Using list comprehension to filter and transform data',
                'language' => 'Python',
                'code' => "# List comprehension example\nnumbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\n\n# Get even squares\neven_squares = [x**2 for x in numbers if x % 2 == 0]\nprint(even_squares)  # [4, 16, 36, 64, 100]",
                'tags' => ['python', 'list', 'comprehension', 'functional']
            ],
            [
                'title' => 'Python Dictionary Operations',
                'description' => 'Common operations with Python dictionaries',
                'language' => 'Python',
                'code' => "# Dictionary operations\nuser = {\n    'name': 'John',\n    'age': 30,\n    'is_admin': False\n}\n\n# Adding/updating items\nuser['email'] = 'john@example.com'\n\n# Getting with default value\nphone = user.get('phone', 'Not provided')\n\n# Dictionary comprehension\nsquares = {x: x**2 for x in range(6)}\nprint(squares)  # {0: 0, 1: 1, 2: 4, 3: 9, 4: 16, 5: 25}",
                'tags' => ['python', 'dictionary', 'data-structure']
            ],

            // PHP Snippets
            [
                'title' => 'PHP Array Functions',
                'description' => 'Using PHP array_map, array_filter and array_reduce',
                'language' => 'PHP',
                'code' => "<?php\n// Sample array\n\$numbers = [1, 2, 3, 4, 5];\n\n// Using array_map\n\$squared = array_map(function(\$n) {\n    return \$n * \$n;\n}, \$numbers);\n\n// Using array_filter\n\$even = array_filter(\$numbers, function(\$n) {\n    return \$n % 2 === 0;\n});\n\n// Using array_reduce\n\$sum = array_reduce(\$numbers, function(\$carry, \$n) {\n    return \$carry + \$n;\n}, 0);\n\nprint_r(\$squared); // [1, 4, 9, 16, 25]\nprint_r(\$even); // [2, 4]\necho \$sum; // 15\n?>",
                'tags' => ['php', 'array', 'functional']
            ],
            [
                'title' => 'PHP PDO Database Connection',
                'description' => 'Secure database connection using PDO in PHP',
                'language' => 'PHP',
                'code' => "<?php\n// PDO Database Connection\ntry {\n    \$host = 'localhost';\n    \$dbname = 'test_db';\n    \$username = 'db_user';\n    \$password = 'db_password';\n    \n    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$dbname\", \$username, \$password);\n    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);\n    \n    echo \"Connected successfully\";\n} catch(PDOException \$e) {\n    echo \"Connection failed: \" . \$e->getMessage();\n}\n?>",
                'tags' => ['php', 'pdo', 'database', 'mysql']
            ],

            // Java Snippets
            [
                'title' => 'Java Stream API',
                'description' => 'Using Java Stream API for data processing',
                'language' => 'Java',
                'code' => "import java.util.Arrays;\nimport java.util.List;\nimport java.util.stream.Collectors;\n\npublic class StreamExample {\n    public static void main(String[] args) {\n        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);\n        \n        // Filter, map and collect\n        List<Integer> evenSquares = numbers.stream()\n            .filter(n -> n % 2 == 0)\n            .map(n -> n * n)\n            .collect(Collectors.toList());\n            \n        System.out.println(evenSquares); // [4, 16, 36, 64, 100]\n        \n        // Sum\n        int sum = numbers.stream().reduce(0, Integer::sum);\n        System.out.println(\"Sum: \" + sum); // 55\n    }\n}",
                'tags' => ['java', 'stream', 'functional', 'lambda']
            ],

            // SQL Snippets
            [
                'title' => 'SQL JOIN Query',
                'description' => 'Example of SQL JOIN operations',
                'language' => 'SQL',
                'code' => "-- Sample tables: users and orders\n\n-- INNER JOIN: Get users with their orders\nSELECT u.id, u.name, o.order_id, o.amount\nFROM users u\nINNER JOIN orders o ON u.id = o.user_id\nORDER BY u.id;\n\n-- LEFT JOIN: Get all users with their orders (if any)\nSELECT u.id, u.name, o.order_id, o.amount\nFROM users u\nLEFT JOIN orders o ON u.id = o.user_id\nORDER BY u.id;\n\n-- Get number of orders per user\nSELECT u.id, u.name, COUNT(o.order_id) as order_count, SUM(o.amount) as total_spent\nFROM users u\nLEFT JOIN orders o ON u.id = o.user_id\nGROUP BY u.id, u.name\nORDER BY total_spent DESC;",
                'tags' => ['sql', 'join', 'database', 'query']
            ],

            // HTML Snippets
            [
                'title' => 'HTML Form Template',
                'description' => 'Basic responsive HTML form template',
                'language' => 'HTML',
                'code' => "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Contact Form</title>\n    <style>\n        .form-group { margin-bottom: 15px; }\n        label { display: block; margin-bottom: 5px; }\n        input, textarea { width: 100%; padding: 8px; box-sizing: border-box; }\n        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }\n    </style>\n</head>\n<body>\n    <h2>Contact Form</h2>\n    <form action=\"/submit\" method=\"post\">\n        <div class=\"form-group\">\n            <label for=\"name\">Name:</label>\n            <input type=\"text\" id=\"name\" name=\"name\" required>\n        </div>\n        <div class=\"form-group\">\n            <label for=\"email\">Email:</label>\n            <input type=\"email\" id=\"email\" name=\"email\" required>\n        </div>\n        <div class=\"form-group\">\n            <label for=\"message\">Message:</label>\n            <textarea id=\"message\" name=\"message\" rows=\"5\" required></textarea>\n        </div>\n        <button type=\"submit\">Submit</button>\n    </form>\n</body>\n</html>",
                'tags' => ['html', 'form', 'css', 'responsive']
            ],

            // C# Snippets
            [
                'title' => 'C# LINQ Examples',
                'description' => 'Common LINQ operations in C#',
                'language' => 'C#',
                'code' => "using System;\nusing System.Collections.Generic;\nusing System.Linq;\n\npublic class LinqExamples\n{\n    public static void Main()\n    {\n        List<int> numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };\n        \n        // Where (Filter)\n        var evenNumbers = numbers.Where(n => n % 2 == 0);\n        Console.WriteLine(string.Join(\", \", evenNumbers)); // 2, 4, 6, 8, 10\n        \n        // Select (Map)\n        var squared = numbers.Select(n => n * n);\n        Console.WriteLine(string.Join(\", \", squared));\n        \n        // OrderBy\n        var ordered = numbers.OrderByDescending(n => n);\n        Console.WriteLine(string.Join(\", \", ordered));\n        \n        // Aggregate functions\n        var sum = numbers.Sum();\n        var avg = numbers.Average();\n        Console.WriteLine(\\$\"Sum: {sum}, Average: {avg}\");\n    }\n}",
                'tags' => ['csharp', 'linq', 'collections', 'dotnet']
            ],

            // React JavaScript
            [
                'title' => 'React Functional Component',
                'description' => 'Simple React functional component with hooks',
                'language' => 'JavaScript',
                'code' => "import React, { useState, useEffect } from 'react';\n\nconst UserProfile = ({ userId }) => {\n  const [user, setUser] = useState(null);\n  const [loading, setLoading] = useState(true);\n  const [error, setError] = useState(null);\n\n  useEffect(() => {\n    const fetchUser = async () => {\n      try {\n        setLoading(true);\n        const response = await fetch(`https://api.example.com/users/\${userId}`);\n        if (!response.ok) throw new Error('Failed to fetch user');\n        \n        const userData = await response.json();\n        setUser(userData);\n        setError(null);\n      } catch (err) {\n        setError(err.message);\n      } finally {\n        setLoading(false);\n      }\n    };\n\n    fetchUser();\n  }, [userId]);\n\n  if (loading) return <div>Loading...</div>;\n  if (error) return <div>Error: {error}</div>;\n  if (!user) return <div>No user found</div>;\n\n  return (\n    <div className=\"user-profile\">\n      <h2>{user.name}</h2>\n      <p>Email: {user.email}</p>\n      <p>Location: {user.location}</p>\n    </div>\n  );\n};\n\nexport default UserProfile;",
                'tags' => ['javascript', 'react', 'hooks', 'frontend']
            ],

            // CSS Snippet
            [
                'title' => 'CSS Flexbox Layout',
                'description' => 'Common flexbox patterns for responsive layouts',
                'language' => 'CSS',
                'code' => "/* Basic Flexbox Container */\n.flex-container {\n  display: flex;\n  flex-wrap: wrap;\n  justify-content: space-between;\n}\n\n/* Flex Items */\n.flex-item {\n  flex: 1 1 300px; /* grow shrink basis */\n  margin: 10px;\n  padding: 20px;\n  background-color: #f5f5f5;\n}\n\n/* Responsive Card Layout */\n.card-grid {\n  display: flex;\n  flex-wrap: wrap;\n  gap: 20px;\n}\n\n.card {\n  flex: 1 1 calc(33.333% - 20px);\n  min-width: 300px;\n  border-radius: 8px;\n  box-shadow: 0 2px 5px rgba(0,0,0,0.1);\n  padding: 20px;\n}\n\n/* Navbar using Flexbox */\n.navbar {\n  display: flex;\n  justify-content: space-between;\n  align-items: center;\n  padding: 15px 20px;\n  background-color: #333;\n  color: white;\n}\n\n.nav-links {\n  display: flex;\n  gap: 20px;\n}\n\n/* Mobile Responsive */\n@media (max-width: 768px) {\n  .navbar {\n    flex-direction: column;\n    gap: 10px;\n  }\n  \n  .card {\n    flex: 1 1 100%;\n  }\n}",
                'tags' => ['css', 'flexbox', 'responsive', 'layout']
            ],

            // Dart/Flutter
            [
                'title' => 'Flutter StatefulWidget',
                'description' => 'Basic Flutter StatefulWidget example',
                'language' => 'Dart',
                'code' => "import 'package:flutter/material.dart';\n\nclass CounterWidget extends StatefulWidget {\n  const CounterWidget({Key? key}) : super(key: key);\n\n  @override\n  _CounterWidgetState createState() => _CounterWidgetState();\n}\n\nclass _CounterWidgetState extends State<CounterWidget> {\n  int _counter = 0;\n\n  void _incrementCounter() {\n    setState(() {\n      _counter++;\n    });\n  }\n\n  @override\n  Widget build(BuildContext context) {\n    return Scaffold(\n      appBar: AppBar(\n        title: Text('Counter Example'),\n      ),\n      body: Center(\n        child: Column(\n          mainAxisAlignment: MainAxisAlignment.center,\n          children: <Widget>[\n            Text(\n              'You have pushed the button this many times:',\n            ),\n            Text(\n              '\$_counter',\n              style: Theme.of(context).textTheme.headline4,\n            ),\n          ],\n        ),\n      ),\n      floatingActionButton: FloatingActionButton(\n        onPressed: _incrementCounter,\n        tooltip: 'Increment',\n        child: Icon(Icons.add),\n      ),\n    );\n  }\n}",
                'tags' => ['dart', 'flutter', 'mobile', 'widget']
            ],

            // TypeScript
            [
                'title' => 'TypeScript Interfaces',
                'description' => 'Using TypeScript interfaces for type safety',
                'language' => 'TypeScript',
                'code' => "// Define interfaces\ninterface User {\n  id: number;\n  name: string;\n  email: string;\n  role: Role;\n  createdAt: Date;\n}\n\nenum Role {\n  Admin = 'ADMIN',\n  User = 'USER',\n  Editor = 'EDITOR'\n}\n\ninterface Post {\n  id: number;\n  title: string;\n  content: string;\n  authorId: number;\n  published: boolean;\n}\n\n// Use interfaces in functions\nfunction createUser(userData: Omit<User, 'id' | 'createdAt'>): User {\n  return {\n    ...userData,\n    id: Math.floor(Math.random() * 1000),\n    createdAt: new Date()\n  };\n}\n\nconst newUser = createUser({\n  name: 'John Doe',\n  email: 'john@example.com',\n  role: Role.User\n});\n\nconsole.log(newUser);",
                'tags' => ['typescript', 'interfaces', 'type-safety']
            ],

            // Ruby
            [
                'title' => 'Ruby Array Methods',
                'description' => 'Common Ruby array operations',
                'language' => 'Ruby',
                'code' => "# Sample array\nnumbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\n\n# Map (transform)\nsquared = numbers.map { |n| n * n }\nputs squared.inspect  # [1, 4, 9, 16, 25, 36, 49, 64, 81, 100]\n\n# Select (filter)\neven = numbers.select { |n| n.even? }\nputs even.inspect  # [2, 4, 6, 8, 10]\n\n# Reduce (aggregate)\nsum = numbers.reduce(0) { |acc, n| acc + n }\nputs sum  # 55\n\n# Chaining methods\neven_squared_sum = numbers\n  .select { |n| n.even? }\n  .map { |n| n * n }\n  .reduce(0) { |acc, n| acc + n }\n\nputs even_squared_sum  # 220",
                'tags' => ['ruby', 'array', 'enumerable', 'functional']
            ],

            // Go
            [
                'title' => 'Go Goroutines and Channels',
                'description' => 'Concurrency with goroutines and channels in Go',
                'language' => 'Go',
                'code' => "package main\n\nimport (\n\t\"fmt\"\n\t\"time\"\n)\n\nfunc worker(id int, jobs <-chan int, results chan<- int) {\n\tfor j := range jobs {\n\t\tfmt.Printf(\"Worker %d started job %d\\n\", id, j)\n\t\ttime.Sleep(time.Second) // Simulate work\n\t\tfmt.Printf(\"Worker %d finished job %d\\n\", id, j)\n\t\tresults <- j * 2 // Send result\n\t}\n}\n\nfunc main() {\n\tjobs := make(chan int, 5)\n\tresults := make(chan int, 5)\n\n\t// Start workers\n\tfor w := 1; w <= 3; w++ {\n\t\tgo worker(w, jobs, results)\n\t}\n\n\t// Send jobs\n\tfor j := 1; j <= 5; j++ {\n\t\tjobs <- j\n\t}\n\tclose(jobs)\n\n\t// Collect results\n\tfor a := 1; a <= 5; a++ {\n\t\t<-results\n\t}\n}",
                'tags' => ['go', 'concurrency', 'goroutines', 'channels']
            ],

            // Swift
            [
                'title' => 'Swift Closures',
                'description' => 'Using closures in Swift',
                'language' => 'Swift',
                'code' => "import Foundation\n\n// Sample array\nlet numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\n\n// Map\nlet squared = numbers.map { \\$0 * \\$0 }\nprint(squared) // [1, 4, 9, 16, 25, 36, 49, 64, 81, 100]\n\n// Filter\nlet evenNumbers = numbers.filter { \\$0 % 2 == 0 }\nprint(evenNumbers) // [2, 4, 6, 8, 10]\n\n// Reduce\nlet sum = numbers.reduce(0, { \\$0 + \\$1 })\nprint(sum) // 55\n\n// Sorted\nlet descendingOrder = numbers.sorted(by: >)\nprint(descendingOrder) // [10, 9, 8, 7, 6, 5, 4, 3, 2, 1]\n\n// Custom closure\nfunc performOperation(on numbers: [Int], using operation: (Int) -> Int) -> [Int] {\n    return numbers.map(operation)\n}\n\nlet doubled = performOperation(on: numbers) { \\$0 * 2 }\nprint(doubled) // [2, 4, 6, 8, 10, 12, 14, 16, 18, 20]",
                'tags' => ['swift', 'closures', 'functional', 'ios']
            ]
        ];
    }
}
