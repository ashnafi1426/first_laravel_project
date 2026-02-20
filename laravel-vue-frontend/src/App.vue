<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const posts = ref([])
const title = ref('')
const content = ref('')
const editingId = ref(null)
const errors = ref({})

const API = 'http://127.0.0.1:8000/api/posts'

// FETCH POSTS
const fetchPosts = async () => {
    const res = await axios.get(API)
    posts.value = res.data
}

// CREATE POST
const createPost = async () => {
    errors.value = {}
    
    try {
        await axios.post(API, {
            title: title.value,
            content: content.value
        })
        resetForm()
        fetchPosts()
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        }
    }
}

// EDIT MODE
const editPost = (post) => {
    title.value = post.title
    content.value = post.content
    editingId.value = post.id
    errors.value = {}
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

// UPDATE POST
const updatePost = async () => {
    errors.value = {}
    
    try {
        await axios.put(`${API}/${editingId.value}`, {
            title: title.value,
            content: content.value
        })
        resetForm()
        fetchPosts()
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        }
    }
}

// DELETE POST
const deletePost = async (id) => {
    if (confirm('Are you sure you want to delete this post?')) {
        await axios.delete(`${API}/${id}`)
        fetchPosts()
    }
}

// RESET FORM
const resetForm = () => {
    title.value = ''
    content.value = ''
    editingId.value = null
    errors.value = {}
}

// CLEAR ERRORS WHEN TYPING
watch([title, content], () => {
    errors.value = {}
})

onMounted(fetchPosts)
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-gray-900 mb-3">
          <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
            Blog Manager
          </span>
        </h1>
        <p class="text-xl text-gray-600">Create and manage your blog posts with ease</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column - Form -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-xl p-8 sticky top-8">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-bold text-gray-800">
                {{ editingId ? 'Edit Post' : 'Create Post' }}
              </h2>
              <span 
                :class="editingId ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'"
                class="text-xs font-semibold px-3 py-1 rounded-full"
              >
                {{ editingId ? 'Editing' : 'New' }}
              </span>
            </div>

            <form @submit.prevent="editingId ? updatePost() : createPost()" class="space-y-6">
              <!-- Title Input -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Post Title
                </label>
                <input 
                  v-model="title"
                  type="text" 
                  placeholder="Enter an engaging title..."
                  :class="errors.title ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'"
                  class="w-full px-4 py-3 bg-gray-50 border rounded-xl focus:ring-2 focus:border-transparent outline-none transition duration-200"
                />
                <div v-if="errors.title" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  {{ errors.title[0] }}
                </div>
              </div>

              <!-- Content Textarea -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Content
                </label>
                <textarea 
                  v-model="content"
                  rows="8"
                  placeholder="Write your story..."
                  :class="errors.content ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'"
                  class="w-full px-4 py-3 bg-gray-50 border rounded-xl focus:ring-2 focus:border-transparent outline-none transition duration-200 resize-none"
                ></textarea>
                <div v-if="errors.content" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  {{ errors.content[0] }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-3">
                <button 
                  v-if="!editingId"
                  type="submit"
                  class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                  <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Publish Post
                  </span>
                </button>
                
                <button 
                  v-else
                  type="submit"
                  class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                  <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Post
                  </span>
                </button>
                
                <!-- Cancel Button -->
                <button 
                  v-if="editingId"
                  type="button"
                  @click="resetForm"
                  class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition duration-200"
                >
                  Cancel
                </button>
              </div>
            </form>

            <!-- Stats -->
            <div class="mt-8 pt-6 border-t border-gray-200">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Total Posts</span>
                <span class="font-bold text-gray-900">{{ posts.length }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Posts List -->
        <div class="lg:col-span-2">
          <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Recent Posts</h2>
            <span class="text-sm text-gray-500">{{ posts.length }} {{ posts.length === 1 ? 'article' : 'articles' }}</span>
          </div>

          <!-- Empty State -->
          <div v-if="posts.length === 0" class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No posts yet</h3>
            <p class="text-gray-600 mb-6">Create your first blog post to get started!</p>
          </div>

          <!-- Posts List -->
          <div v-else class="space-y-6">
            <article 
              v-for="post in posts" 
              :key="post.id" 
              class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 overflow-hidden group"
            >
              <div class="p-8">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">
                      {{ post.title }}
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                      <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Just now
                      </span>
                    </div>
                  </div>
                  <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                    Published
                  </span>
                </div>
                
                <p class="text-gray-600 leading-relaxed mb-6">
                  {{ post.content }}
                </p>
                
                <div class="flex items-center gap-3">
                  <button 
                    @click="editPost(post)"
                    class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                  </button>
                  <button 
                    @click="deletePost(post.id)"
                    class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                  </button>
                </div>
              </div>
            </article>
          </div>

        </div>

      </div>

    </div>
  </div>
</template>
