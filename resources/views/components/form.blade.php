<div class="container" x-data="controller()"
    data-action="{{ route('component.form') }}">
    <form @submit.prevent="save($el)">

        <!-- Example 1 -->
        <label for="email" class="label">Email:</label>
        <input id="email" type="email" name="email" class="input">
        <label for="password">Password: </label>
        <input id="password" type="password" class="input">

        <input class="button" type="submit" value="Send">


        <!-- Example 2 --->
        <label for="view">Select view to render</label>
        <select id="view" class="select" @change="render($el)" name="view" >
            <option value="list">List</option>
            <option value="form">Form</option>
        </select>


        <!-- Example 3 -->
        <div x-html="(await request('render', {view: 'list'})).html"></div>


    </form>
    <div x-ref="view">

    </div>
    <p x-show="error" x-html="error"></p>
</div>


<script>
    function controller() {
        return {
            error: '',

            render(el) {
                this.request('render', { view: el.value })
                    .then(res => this.$refs.view.innerHTML = res.html)
                    .catch(err => this.error = err.error)
            },

            save(form) {
                let formData = Object.fromEntries(new FormData(form))

                this.request('save', formData)
                    .then(res => { /* handle response */ })
                    .catch(err => { /* handle error */ })
            },


            request(method, payload) {
                return sendRequest(this.$root.dataset.action, method, payload)
            }

        }
    }


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

</script>
