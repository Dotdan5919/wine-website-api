<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog Posts Management</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4c63d2 0%, #5a67d8 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .content {
            padding: 30px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .post-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .post-card .post-header {
            padding: 20px;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-bottom: 1px solid #e2e8f0;
        }

        .post-card .post-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .post-card .post-meta {
            color: #718096;
            font-size: 0.9rem;
        }

        .post-card .post-content {
            padding: 20px;
        }

        .post-card .post-excerpt {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post-card .post-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #718096;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #f56565;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #718096;
        }

        .loading i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .no-posts {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .no-posts i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #cbd5e0;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #c6f6d5;
            color: #276749;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        .file-upload-container {
            position: relative;
        }

        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: #f7fafc;
        }

        .file-upload-area.dragover {
            border-color: #667eea;
            background: #ebf8ff;
        }

        .upload-placeholder {
            color: #718096;
        }

        .upload-placeholder i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #a0aec0;
        }

        .upload-placeholder p {
            margin-bottom: 5px;
            font-weight: 500;
        }

        .upload-placeholder small {
            color: #a0aec0;
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .remove-image {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #f56565;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .remove-image:hover {
            background: #e53e3e;
            transform: scale(1.1);
        }

        .post-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: none;
            }

            .posts-grid {
                grid-template-columns: 1fr;
            }

            .post-card .post-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container" x-data="blogManager()">
        <div class="header">
            <h1><i class="fas fa-blog"></i> Blog Posts Management</h1>
            <p>Create, Read, Update, and Delete your blog posts</p>
        </div>

        <div class="content">
            <!-- Alert Messages -->
            <div x-show="alert.show" x-transition class="alert" :class="alert.type === 'success' ? 'alert-success' : 'alert-error'">
                <span x-text="alert.message"></span>
            </div>

            <!-- Controls -->
            <div class="controls">
                <div class="search-box">
                    <input type="text" x-model="searchQuery" placeholder="Search posts..." @input="filterPosts">
                    <i class="fas fa-search"></i>
                </div>
                <button class="btn btn-primary" @click="openCreateModal">
                    <i class="fas fa-plus"></i> Create New Post
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading posts...</p>
            </div>

            <!-- Posts Grid -->
            <div x-show="!loading && filteredPosts.length > 0" class="posts-grid">
                <template x-for="post in filteredPosts" :key="post.id">
                    <div class="post-card">
                        <div class="post-header">
                            <h3 class="post-title" x-text="post.title"></h3>
                            <div class="post-meta">
                                <i class="fas fa-calendar"></i>
                                <span x-text="formatDate(post.created_at)"></span>
                            </div>
                        </div>
                        <div class="post-content">
                            <div x-show="post.featured_image">
                                <img :src="getImageUrl(post.featured_image)" :alt="post.title" class="post-image">
                            </div>
                            <p class="post-excerpt" x-text="truncateText(post.content, 150)"></p>
                            <div class="post-actions">
                                <button class="btn btn-secondary" @click="viewPost(post)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-warning" @click="editPost(post)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger" @click="deletePost(post.id)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- No Posts State -->
            <div x-show="!loading && filteredPosts.length === 0" class="no-posts">
                <i class="fas fa-file-alt"></i>
                <h3>No posts found</h3>
                <p>Start by creating your first blog post!</p>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div class="modal" :class="{ active: showModal }" @click.self="closeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" x-text="isEditing ? 'Edit Post' : 'Create New Post'"></h2>
                    <button class="close-modal" @click="closeModal">&times;</button>
                </div>
                <form @submit.prevent="submitPost">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" x-model="form.title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" x-model="form.content" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="excerpt">Excerpt (Optional)</label>
                        <textarea id="excerpt" x-model="form.excerpt" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="featured_image">Featured Image</label>
                        <div class="file-upload-container">
                            <div class="file-upload-area" @click="$refs.fileInput.click()" @dragover.prevent="dragOver" @dragleave.prevent="dragLeave" @drop.prevent="handleFileDrop">
                                <div x-show="!form.imagePreview" class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to upload or drag and drop</p>
                                    <small>PNG, JPG, GIF up to 5MB</small>
                                </div>
                                <div x-show="form.imagePreview" class="image-preview-container">
                                    <img :src="form.imagePreview" alt="Preview" class="image-preview">
                                    <button type="button" class="remove-image" @click.stop="removeImage">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="file" x-ref="fileInput" @change="handleFileUpload" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-success" :disabled="submitting">
                            <i class="fas" :class="submitting ? 'fa-spinner fa-spin' : (isEditing ? 'fa-save' : 'fa-plus')"></i>
                            <span x-text="submitting ? 'Saving...' : (isEditing ? 'Update Post' : 'Create Post')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div class="modal" :class="{ active: showViewModal }" @click.self="closeViewModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" x-text="viewingPost.title"></h2>
                    <button class="close-modal" @click="closeViewModal">&times;</button>
                </div>
                <div class="post-meta" style="margin-bottom: 20px; color: #718096;">
                    <i class="fas fa-calendar"></i>
                    <span x-text="formatDate(viewingPost.created_at)"></span>
                </div>
                <div x-show="viewingPost.featured_image" style="margin-bottom: 20px;">
                    <img :src="getImageUrl(viewingPost.featured_image)" :alt="viewingPost.title" class="post-image">
                </div>
                <div style="line-height: 1.6; white-space: pre-wrap;" x-text="viewingPost.content"></div>
                <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button class="btn btn-warning" @click="editPost(viewingPost)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-secondary" @click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function blogManager() {
            return {
                posts: [],
                filteredPosts: [],
                loading: false,
                submitting: false,
                showModal: false,
                showViewModal: false,
                isEditing: false,
                searchQuery: '',
                viewingPost: {},
                baseUrl: window.location.origin + '/api',
                form: {
                    id: null,
                    title: '',
                    content: '',
                    excerpt: '',
                    featured_image: null,
                    imagePreview: null
                },
                alert: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                init() {
                    this.loadPosts();
                    this.setupCSRF();
                },

                setupCSRF() {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (token) {
                        this.csrfToken = token;
                    }
                },

                getHeaders(isFormData = false) {
                    const headers = {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    };
                    
                        let authToken = localStorage.getItem('authToken') ;

                    if (!isFormData) {
                        headers['Content-Type'] = 'application/json';
                    }
                    
                        if (authToken!='') {
            headers['Authorization'] = `Bearer ${authToken}`;
        }
                    return headers;
                },

                async loadPosts() {
                    this.loading = true;
                    try {
                        const response = await fetch(`${this.baseUrl}/posts`, {
                            headers: this.getHeaders()
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        this.posts = data.data || data;
                        this.filteredPosts = this.posts;
                    } catch (error) {
                        this.showAlert('Failed to load posts: ' + error.message, 'error');
                        console.error('Load posts error:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async submitPost() {
                    if (!this.form.title || !this.form.content) {
                        this.showAlert('Please fill in all required fields', 'error');
                        return;
                    }

                    this.submitting = true;
                    
                    try {
                        const url = this.isEditing ? `${this.baseUrl}/posts/${this.form.id}` : `${this.baseUrl}/posts`;
                        
                        const formData = new FormData();
                        formData.append('title', this.form.title);
                        formData.append('content', this.form.content);
                        formData.append('excerpt', this.form.excerpt || '');
                        
                        if (this.form.featured_image) {
                            formData.append('featured_image', this.form.featured_image);
                        }
                        
                        if (this.isEditing) {
                            formData.append('_method', 'PUT');
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: this.getHeaders(true),
                            body: formData
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Failed to save post');
                        }

                        const result = await response.json();
                        const savedPost = result.data || result;
                        
                        if (this.isEditing) {
                            const index = this.posts.findIndex(p => p.id === this.form.id);
                            if (index !== -1) {
                                this.posts[index] = savedPost;
                            }
                            this.showAlert('Post updated successfully!', 'success');
                        } else {
                            this.posts.unshift(savedPost);
                            this.showAlert('Post created successfully!', 'success');
                        }

                        this.filterPosts();
                        this.closeModal();
                        
                    } catch (error) {
                        this.showAlert('Error saving post: ' + error.message, 'error');
                        console.error('Submit post error:', error);
                    } finally {
                        this.submitting = false;
                    }
                },

                async deletePost(postId) {
                    if (!confirm('Are you sure you want to delete this post?')) return;

                    try {
                        const response = await fetch(`${this.baseUrl}/posts/${postId}`, {
                            method: 'DELETE',
                            headers: this.getHeaders()
                        });

                        if (!response.ok) {
                            throw new Error('Failed to delete post');
                        }

                        this.posts = this.posts.filter(p => p.id !== postId);
                        this.filterPosts();
                        this.showAlert('Post deleted successfully!', 'success');
                        
                    } catch (error) {
                        this.showAlert('Error deleting post: ' + error.message, 'error');
                        console.error('Delete post error:', error);
                    }
                },

                openCreateModal() {
                    this.resetForm();
                    this.isEditing = false;
                    this.showModal = true;
                },

                editPost(post) {
                    this.form = {
                        id: post.id,
                        title: post.title,
                        content: post.content,
                        excerpt: post.excerpt || '',
                        featured_image: null,
                        imagePreview: post.featured_image ? this.getImageUrl(post.featured_image) : null
                    };
                    this.isEditing = true;
                    this.showModal = true;
                    this.showViewModal = false;
                },

                viewPost(post) {
                    this.viewingPost = post;
                    this.showViewModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.resetForm();
                },

                closeViewModal() {
                    this.showViewModal = false;
                    this.viewingPost = {};
                },

                resetForm() {
                    this.form = {
                        id: null,
                        title: '',
                        content: '',
                        excerpt: '',
                        featured_image: null,
                        imagePreview: null
                    };
                },

                filterPosts() {
                    if (!this.searchQuery) {
                        this.filteredPosts = this.posts;
                        return;
                    }

                    const query = this.searchQuery.toLowerCase();
                    this.filteredPosts = this.posts.filter(post => 
                        post.title.toLowerCase().includes(query) ||
                        post.content.toLowerCase().includes(query) ||
                        (post.excerpt && post.excerpt.toLowerCase().includes(query))
                    );
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    return new Date(dateString).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                truncateText(text, length) {
                    if (!text) return '';
                    return text.length > length ? text.substring(0, length) + '...' : text;
                },

                getImageUrl(filename) {
                    if (!filename) return '';
                    if (filename.startsWith('http')) return filename;
                    return `${window.location.origin}/storage/uploads/${filename}`;
                },

                showAlert(message, type = 'success') {
                    this.alert = {
                        show: true,
                        message: message,
                        type: type
                    };
                    
                    setTimeout(() => {
                        this.alert.show = false;
                    }, 5000);
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.processFile(file);
                    }
                },

                dragOver(event) {
                    event.target.classList.add('dragover');
                },

                dragLeave(event) {
                    event.target.classList.remove('dragover');
                },

                handleFileDrop(event) {
                    event.preventDefault();
                    event.target.classList.remove('dragover');
                    
                    const file = event.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        this.processFile(file);
                    }
                },

                processFile(file) {
                    if (file.size > 5 * 1024 * 1024) {
                        this.showAlert('File size must be less than 5MB', 'error');
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        this.showAlert('Please select an image file', 'error');
                        return;
                    }

                    this.form.featured_image = file;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.form.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                removeImage() {
                    this.form.featured_image = null;
                    this.form.imagePreview = null;
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                }
            };
        }
    </script>
</body>
</html>