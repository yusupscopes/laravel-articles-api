<template>
    <div>
        <h2>Articles</h2>
        <form @submit.prevent="addArticle" class="mb-3">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Title of post"
                v-model="article.title">
            </div>
            <div class="form-group">
                <textarea cols="30" rows="10" class="form-control" placeholder="Body of post"
                v-model="article.body"></textarea>
            </div>
            <button type="submit" class="btn btn-secondary btn-block">Save</button>
        </form>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li v-bind:class="{disabled: !pagination.prev_page_url}" class="page-item">
                    <a @click="fetchArticles(pagination.prev_page_url)" class="page-link" href="#">Previous</a>
                </li>

                <li class="page-item disabled">
                    <a href="#" class="page-link text-dark">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </a>
                </li>
                
                <li v-bind:class="{disabled: !pagination.next_page_url}" class="page-item">
                    <a @click="fetchArticles(pagination.next_page_url)" class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
        <div v-for="article in articles" v-bind:key="article.id" class="card mb-2">
            <div class="card-header">
                <h3>{{ article.title }}</h3>
            </div>

            <div class="card-body">
                <p>{{ article.body }}</p>
                <p><b>published</b>: {{ article.created_at }}</p>
                <hr>
                <button @click="editArticle(article)" class="btn btn-warning">Edit</button>
                <button @click="deleteArticle(article.id)" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                articles:[],
                article: {
                    id:'',
                    title:'',
                    body:'',
                    created_at:''
                },
                pagination: {},
                edit: false
            }
        },
        methods: {
            fetchArticles(page_url) {
                page_url = page_url || '/api/articles'
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.articles = res.data
                        this.makePagination(res.meta, res.links)
                    })
                    .catch(err => console.log(err))
                // axios.get('/api/articles')
                //     .then(res => {
                //         console.log(res.data.data)
                //     })
            },
            addArticle() {
                if (this.edit === false) {
                    // Add
                    fetch('/api/articles', {
                        method: 'POST',
                        body: JSON.stringify(this.article),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.article.title = ''
                        this.article.body  = ''
                        alert('Article was added!')
                        this.fetchArticles()
                    })
                } else {
                    // update
                    fetch(`/api/articles/${this.article.id}`, {
                        method: 'PUT',
                        body: JSON.stringify(this.article),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.article.title = ''
                        this.article.body = ''
                        alert('Article Updated')
                        this.fetchArticles()
                    })
                    .catch(err => console.log(err))
                }
            },
            editArticle(article) {
                this.edit = true
                this.article.id = article.id
                this.article.title = article.title
                this.article.body = article.body
            },
            deleteArticle(id) {
                if (confirm('Apakah anda yakin?')) {
                    fetch(`/api/articles/${id}`, {
                        method: 'DELETE'
                    }).then(res => res.json())
                    .then(data => {
                        alert('Article removed')
                        this.fetchArticles()
                    }).catch(err => console.log(err))
                }
            },
            makePagination(meta, links) {
                let pagination = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: links.next,
                    prev_page_url: links.prev
                }

                this.pagination = pagination
            },
        },

        created() {
            this.fetchArticles()
        }
    }
</script>