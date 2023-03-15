# Alpine Components

The most important thing about AlpineJs + PHP components is the SendRequest method. 

```js
 // TODO: Move to app.js
    function sendRequest(url, method, payload) {
        payload.method = method
        payload._token = @js(csrf_token())
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        }).then(res => {
            if(res.ok) return res.json()
            return Promise.reject(res.json())
        })
    }
```

Now we can start building components 

Basic structure:
```html
<div x-data="controller()"
    data-action="{{ route('component.form') }}">
    
    <!--- Amazing things whit AlpineJS --->
    
</div>
```

Js Controller:
```js
 function controller() {
        return {
            request(method, payload) {
                return sendRequest(this.$root.dataset.action, method, payload)
            }
            
            /* other functions...  */
        }
    }
```

PHP Controller
```php
class Component extends Controller
{
    public function __invoke(Request $request)
    {
        $method = $request->input('method');

        if (!method_exists($this, $method)) {
            return response()->json(['error' => 'Invalid Method'], 400);
        }

        return $this->{$method}($request);
    }
    
    /** Methods */
}
```

## Examples: 

### 1. Send form data:
```html
<div x-data="controller()"
     data-action="{{ route('component.form') }}">
    <form @submit.prevent="save($el)">
        <label for="email" class="label">Email:</label>
        <input id="email" type="email" name="email" class="input">
        <label for="password">Password:</label>
        <input id="password" type="password" class="input">

        <input class="button" type="submit" value="Send">
    </form>
    <p x-show="error" x-html="error"></p>
</div>
```

```js
function controller() {
    return {
        error: '',
        request(method, payload) {
            return sendRequest(this.$root.dataset.action, method, payload)
        },
        
        save(form) {
            let formData = Object.fromEntries(new FormData(form))
            this.request('save', formData)
                .then(res => { /* handle response */ })
                .catch(err => this.error = err.error)
        }
    }
}
```

### 2. Render Blade Views

```html
<div x-data="controller()"
     data-action="{{ route('component.form') }}">
    <label for="view">Select view to render</label>
    <select id="view" class="select" @change="render($el)" name="view">
        <option value="list">List</option>
        <option value="form">Form</option>
    </select>
    <div x-ref="view"><!-- Render here --> </div>
</div>
```

Js
```js
function controller() {
        return {
            request(method, payload) {
                return sendRequest(this.$root.dataset.action, method, payload)
            },
            
            render(el) {
                this.request('render', { view: el.value })
                    .then(res => this.$refs.view.innerHTML = res.html)
                    .catch(err => this.error = err.error)
            }
        }
    }
```
PHP
```php
 private function render(Request $request) : JsonResponse
    {
        $viewSelected = $request->input('view');

        $users = ['Juan', 'Maria', 'Pedro'];
        $view = view('components.list', compact('users'))
   
        return response()->json([
            'html' => $view->render()
        ]);
    }
```

### 3. Render View Directly

```html
<div x-data="controller()" data-action="{{ route('component.form') }}">
    <div x-html="(await request('render', {view: 'list'})).html"></div>
</div>
```




