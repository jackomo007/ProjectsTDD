<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.css">
</head>
<body>
    <form method="POST" action="/projects" class="container" style="padding-top:40px">
        @csrf
        <h1 class="heading is-1">Create a Project</h1>
        
        <div class="field">
            <label class="label" for="">Title</label>

            <div class="control">
                <input class="input" type="text" name="title" id="" placeholder="Title">
            </div>
        </div>
        <div class="field">
            <label class="label" for="">Description</label>

            <div class="control">
               <textarea class="input" name="description" id="description" cols="30" rows="10">Description</textarea>
            </div>
        </div>
        <div class="field">
            <div class="control">
               <button type="submit" class="button is-link">Create Project</button>
            </div>
        </div>
    </form>
</body>
</html>