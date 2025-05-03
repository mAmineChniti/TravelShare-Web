	const ForumApp = (() => {
		const CONFIG = {
			MAX_LENGTH: 255,
			MIN_POST_LENGTH: 15,
			MIN_COMMENT_LENGTH: 10
		};

		const elements = {
			postText: document.getElementById('postText'),
			postLength: document.getElementById('postLength'),
			postErrorMessage: document.getElementById('postErrorMessage'),
			postForm: document.getElementById('postForm'),
			postImages: document.getElementById('imagePreviewContainer'),

		};

		const CharacterCounter = {
			/**
			 * Set up a character counter for a textarea
			 * @param {HTMLElement} textarea - The textarea element
			 * @param {HTMLElement} counter - The counter element
			 */
			setup(textarea, counter) {
				if (!textarea || !counter) return;
				counter.textContent = `${textarea.value.length}/${CONFIG.MAX_LENGTH}`;

				textarea.addEventListener('input', () => {
					if (textarea.value.length > CONFIG.MAX_LENGTH) {
						textarea.value = textarea.value.slice(0, CONFIG.MAX_LENGTH);
					}
					counter.textContent = `${textarea.value.length}/${CONFIG.MAX_LENGTH}`;
				});
			},

			initAll() {
				if (elements.postText && elements.postLength) {
					this.setup(elements.postText, elements.postLength);
				}

				document.querySelectorAll("textarea[id^='editTextarea-']").forEach(textarea => {
					const id = textarea.id.split("editTextarea-")[1];
					const counter = document.getElementById(`editPostLength-${id}`) ||
								   document.getElementById(`commentLength-${id}`);

					if (counter) {
						this.setup(textarea, counter);
					}
				});

				document.querySelectorAll("textarea[name='commentText']").forEach(textarea => {
					const form = textarea.closest("form");
					if (form) {
						const postId = form.querySelector("input[name='postId']")?.value;
						if (postId) {
							const counter = document.getElementById(`newCommentLength-${postId}`);
							if (counter) {
								this.setup(textarea, counter);
							}
						}
					}
				});
			}
		};

		const Validator = {
			async validatePost() {
				const text = elements.postText.value.trim();

				if (text === '') {
					elements.postErrorMessage.textContent = 'Post cannot be empty.';
					return false;
				}

				if (text.length < CONFIG.MIN_POST_LENGTH) {
					elements.postErrorMessage.textContent = `Post must be at least ${CONFIG.MIN_POST_LENGTH} characters.`;
					return false;
				}

				const isProfanity = await validateProfanity(text);
				if (isProfanity) {
					elements.postErrorMessage.textContent = 'Post contains inappropriate language.';
					return false;
				}

				const files = document.getElementById('postImages').files;
				const isImageProfanity = await validateImageProfanity(files);
				if (isImageProfanity) {
					elements.postErrorMessage.textContent = 'Uploaded images contain inappropriate content.';
					return false;
				}

				elements.postErrorMessage.textContent = '';
				return true;
			},

			/**
			 * Validate a comment form
			 * @param {HTMLFormElement} form - The comment form
			 * @returns {boolean} - Whether the form is valid
			 */
			async validateComment(form) {
				const textarea = form.querySelector("textarea[name='commentText']");
				if (!textarea) return false;

				const postId = form.querySelector("input[name='postId']")?.value;
				if (!postId) return false;

				const errorMessage = document.getElementById(`commentErrorMessage-${postId}`);
				if (!errorMessage) return false;

				const text = textarea.value.trim();

				if (text === '') {
					errorMessage.textContent = 'Comment cannot be empty.';
					return false;
				}

				if (text.length < CONFIG.MIN_COMMENT_LENGTH) {
					errorMessage.textContent = `Comment must be at least ${CONFIG.MIN_COMMENT_LENGTH} characters.`;
					return false;
				}

				const isProfanity = await validateProfanity(text);
				if (isProfanity) {
					errorMessage.textContent = 'Comment contains inappropriate language.';
					return false;
				}

				errorMessage.textContent = '';
				return true;
			},

			/**
			 * Validate an edited post form
			 * @param {HTMLFormElement} form - The edit post form
			 * @param {string} postId - The post ID
			 * @returns {boolean} - Whether the form is valid
			 */
			async validateEditPost(form, postId) {
				const textarea = form.querySelector(`#editTextarea-${postId}`);
				if (!textarea) return false;

				const errorMessage = document.getElementById(`editPostErrorMessage-${postId}`);
				if (!errorMessage) return false;

				const text = textarea.value.trim();

				if (text === '') {
					errorMessage.textContent = 'Post cannot be empty.';
					return false;
				}

				if (text.length < CONFIG.MIN_POST_LENGTH) {
					errorMessage.textContent = `Post must be at least ${CONFIG.MIN_POST_LENGTH} characters.`;
					return false;
				}
				const isProfanity = await validateProfanity(text);
				if (isProfanity) {
					errorMessage.textContent = 'Post contains inappropriate language.';
					return false;
				}
				errorMessage.textContent = '';
				return true;
			},

			/**
			 * Validate an edited comment form
			 * @param {HTMLFormElement} form - The edit comment form
			 * @param {string} commentId - The comment ID
			 * @returns {boolean} - Whether the form is valid
			 */
			async validateEditComment(form, commentId) {
				const textarea = form.querySelector(`#editTextarea-${commentId}`);
				if (!textarea) return false;

				const errorMessage = document.getElementById(`editCommentErrorMessage-${commentId}`);
				if (!errorMessage) return false;

				const text = textarea.value.trim();

				if (text === '') {
					errorMessage.textContent = 'Comment cannot be empty.';
					return false;
				}

				if (text.length < CONFIG.MIN_COMMENT_LENGTH) {
					errorMessage.textContent = `Comment must be at least ${CONFIG.MIN_COMMENT_LENGTH} characters.`;
					return false;
				}
				const isProfanity = await validateProfanity(text);
				if (isProfanity) {
					errorMessage.textContent = 'Comment contains inappropriate language.';
					return false;
				}
				errorMessage.textContent = '';
				return true;
			}
		};

		const FormHandler = {
			async submitPost() {
				const isValid = await Validator.validatePost();
				if (!isValid) {
					return;
				}

				const formData = new FormData(elements.postForm);
				htmx.ajax('POST', '{{ path('app_forum_post') }}', {
					source: elements.postForm,
					target: '#postsContainer',
					swap: 'afterbegin',
					values: Object.fromEntries(formData)
				}).then(() => {
					elements.postForm.reset();
					elements.postImages.innerHTML = '';
					elements.postLength.textContent = `0/${CONFIG.MAX_LENGTH}`;
				});
			},

			/**
			 * Submit an edited post if valid
			 * @param {HTMLFormElement} form - The edit post form
			 * @param {string} postId - The post ID
			 * @returns {boolean} - Whether the form is valid
			 */
			async submitEditPost(form, postId) {
				const isValid = await Validator.validateEditPost(form, postId);
				if(!isValid){
					return;
				}

				const formData = new FormData(form);
				const url = form.getAttribute('action') ||
							`{{ path('app_forum_edit', {'id': '0'}) }}`.replace('0', postId);

				htmx.ajax('POST', url, {
					source: form,
					target: `#postContent-${postId}`,
					swap: 'outerHTML',
					values: Object.fromEntries(formData)
				});

				return false;
			},

			/**
			 * Submit an edited comment if valid
			 * @param {HTMLFormElement} form - The edit comment form
			 * @param {string} commentId - The comment ID
			 * @returns {boolean} - Whether the form is valid
			 */
			async submitEditComment(form, commentId) {
				const isValid = await Validator.validateEditComment(form, commentId);
				if(!isValid){
					return;
				}

				const formData = new FormData(form);
				const url = form.getAttribute('action') ||
							`{{ path('comment_edit', {'id': '0'}) }}`.replace('0', commentId);

				htmx.ajax('POST', url, {
					source: form,
					target: `#commentText-${commentId}`,
					swap: 'outerHTML',
					values: Object.fromEntries(formData)
				});

				return false;
			},

			setupCommentForms() {
				document.querySelectorAll("form[hx-post*='app_forum_comment']").forEach(form => {
					const postUrl = form.getAttribute('hx-post');
					const target = form.getAttribute('hx-target');
					const swap = form.getAttribute('hx-swap');

					form.removeAttribute('hx-post');
					form.removeAttribute('hx-target');
					form.removeAttribute('hx-swap');
					form.removeAttribute('hx-on::after-request');

					const submitBtn = form.querySelector('button[type="submit"]');
					if (!submitBtn) return;

					submitBtn.type = 'button';
					submitBtn.addEventListener('click', () => {
						if (!Validator.validateComment(form)) return;

						const formData = new FormData(form);
						htmx.ajax('POST', postUrl, {
							source: form,
							target: target,
							swap: swap,
							values: Object.fromEntries(formData)
						}).then(() => {
							form.reset();

							const textarea = form.querySelector('textarea[name="commentText"]');
							const postId = form.querySelector("input[name='postId']")?.value;
							if (textarea && postId) {
								const counter = document.getElementById(`newCommentLength-${postId}`);
								if (counter) counter.textContent = `0/${CONFIG.MAX_LENGTH}`;
							}
						});
					});
				});
			}
		};

		const UIHandler = {
			/**
			 * Toggle post editing UI
			 * @param {string} postId - The post ID
			 */
			togglePostEdit(postId) {
				const postContent = document.getElementById(`postContent-${postId}`);
				const editForm = document.getElementById(`editPostForm-${postId}`);
				const editTitle = document.getElementById(`editPostTitleForm-${postId}`);
				const editButton = document.getElementById(`editButton-${postId}`);
				if( postContent) postContent.style.display = 'none';
				if (editForm) editForm.style.display = 'block';
				if (editTitle) editTitle.style.display = 'block';
				if (editButton) editButton.style.display = 'none';
			},

			/**
			 * Toggle comment editing UI
			 * @param {string} commentId - The comment ID
			 */
			toggleCommentEdit(commentId) {
				const commentText = document.getElementById(`commentText-${commentId}`);
				const editForm = document.getElementById(`editCommentForm-${commentId}`);
				const editButton = document.getElementById(`editCommentButton-${commentId}`);

				if (commentText) commentText.style.display = 'none';
				if (editForm) editForm.style.display = 'block';
				if (editButton) editButton.style.display = 'none';
			}
		};

		const ErrorHandler = {
			handleErrors(event) {
				try {
					const response = JSON.parse(event.detail.xhr.responseText);
					console.error('HTMX error response:', response);

					if (!response.error) return;

					const targetId = event.detail.target.id;
					let errorElement;

					if (targetId.startsWith('postText-')) {
						const postId = targetId.split('-')[1];
						errorElement = document.getElementById(`editPostErrorMessage-${postId}`);
					} else if (targetId === 'postsContainer') {
						errorElement = elements.postErrorMessage;
					} else if (targetId.startsWith('commentText-')) {
						const commentId = targetId.split('-')[1];
						errorElement = document.getElementById(`editCommentErrorMessage-${commentId}`);
					} else if (targetId.startsWith('commentsContainer-')) {
						const postId = targetId.split('-')[1];
						errorElement = document.getElementById(`commentErrorMessage-${postId}`);
					}

					if (errorElement) {
						const errorMsg = Array.isArray(response.error)
							? response.error.join(', ')
							: response.error;
						errorElement.textContent = errorMsg;
					}
				} catch (e) {
					console.error('Failed to parse error response:', e);
					console.error('Raw response:', event.detail.xhr.responseText);
				}
			}
		};

		const init = () => {
			CharacterCounter.initAll();

			FormHandler.setupCommentForms();

			document.addEventListener('htmx:responseError', ErrorHandler.handleErrors);

			document.addEventListener('htmx:afterSwap', () => {
				CharacterCounter.initAll();
				FormHandler.setupCommentForms();
			});
		};

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init);
		} else {
			init();
		}

		return {
			submitPostIfValid: FormHandler.submitPost,
			submitEditPostIfValid: FormHandler.submitEditPost,
			submitEditCommentIfValid: FormHandler.submitEditComment,
			editPost: UIHandler.togglePostEdit,
			editComment: UIHandler.toggleCommentEdit,
			validateComment: Validator.validateComment
		};
	})();

	/**
	 * Global function to validate comment form (called from inline onsubmit)
	 * @param {HTMLFormElement} form - The comment form
	 * @returns {boolean} - Whether the form is valid
	 */
	function validateCommentForm(form) {
		return ForumApp.validateComment(form);
	}

	const postTitleInput = document.getElementById('postTitle');
	const postErrorMessage = document.getElementById('postErrorMessage');

	postTitleInput.addEventListener('input', () => {
		const title = postTitleInput.value.trim();
		if (title.length === 0) {
			postErrorMessage.textContent = 'Title cannot be empty.';
		} else if (title.length > 255) {
			postErrorMessage.textContent = 'Title cannot exceed 255 characters.';
		} else {
			postErrorMessage.textContent = '';
		}
	});

	function updateImagePreview(files) {
		const previewContainer = document.getElementById('imagePreviewContainer');
		previewContainer.innerHTML = '';

		Array.from(files).forEach((file, index) => {
			const reader = new FileReader();
			reader.onload = function (e) {
				const div = document.createElement('div');
				div.classList.add('relative', 'w-15', 'h-15', 'overflow-hidden', 'rounded-lg', 'border', 'border-gray-300', 'flex', 'items-center', 'justify-center');

				const img = document.createElement('img');
				img.src = e.target.result;
				img.classList.add('w-full', 'h-full', 'object-cover', 'rounded-lg');

				const removeButton = document.createElement('button');
				removeButton.textContent = 'X';
				removeButton.classList.add('absolute', 'top-0', 'right-0', 'bg-red-500', 'text-white', 'rounded-full', 'w-6', 'h-6', 'flex', 'items-center', 'justify-center', 'text-xs');
				removeButton.addEventListener('click', () => {
					div.remove();
					const dataTransfer = new DataTransfer();
					Array.from(files).forEach((f, i) => {
						if (i !== index) dataTransfer.items.add(f);
					});
					document.getElementById('postImages').files = dataTransfer.files;
				});

				div.appendChild(img);
				div.appendChild(removeButton);
				previewContainer.appendChild(div);
			};
			reader.readAsDataURL(file);
		});
	}

	document.getElementById('postImages').addEventListener('change', function (event) {
		updateImagePreview(event.target.files);
	});

	document.getElementById('dropzone').addEventListener('click', () => {
		document.getElementById('postImages').click();
	});

	document.getElementById('dropzone').addEventListener('dragover', (event) => {
		event.preventDefault();
		event.stopPropagation();
		event.currentTarget.classList.add('dragover');
	});

	document.getElementById('dropzone').addEventListener('dragleave', (event) => {
		event.preventDefault();
		event.stopPropagation();
		event.currentTarget.classList.remove('dragover');
	});

	document.getElementById('dropzone').addEventListener('drop', (event) => {
		event.preventDefault();
		event.stopPropagation();
		event.currentTarget.classList.remove('dragover');

		const files = event.dataTransfer.files;
		const input = document.getElementById('postImages');
		const dataTransfer = new DataTransfer();

		Array.from(input.files).forEach(file => dataTransfer.items.add(file));
		Array.from(files).forEach(file => dataTransfer.items.add(file));

		input.files = dataTransfer.files;
		updateImagePreview(input.files);
	});
	document.addEventListener("DOMContentLoaded", () => {
		const searchInput = document.getElementById("search-input");
		const searchForm = document.getElementById("search-form");
		let searchTimeout;

		searchInput.addEventListener("input", () => {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				searchForm.submit();
			}, 500);
		});

		const sortSelect = document.getElementById("sort-select");
		const sortForm = document.getElementById("sort-form");

		sortSelect.addEventListener("change", () => {
			sortForm.submit();
		});
	});
	async function validateProfanity(text) {
		if(!text) return false;
		if (text.length < 15) return false;
		if (text.length > 255) return false;
		console.log(text);
		const cachedResult = sessionStorage.getItem(`profanity_${btoa(text)}`);
		if (cachedResult !== null) return JSON.parse(cachedResult);
		try {
			const controller = new AbortController();
			const timeoutId = setTimeout(() => {
				controller.abort();
			}, 2000);
			const response = await fetch('https://vector.profanity.dev', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ message: text }),
				signal: controller.signal
			});

			clearTimeout(timeoutId);

			if (!response.ok) {
				throw new Error('Failed to validate profanity');
				return false;
			}

			const result = await response.json();
			sessionStorage.setItem(`profanity_${btoa(text)}`, JSON.stringify(result.isProfanity));
			console.log(result.isProfanity);
			return result.isProfanity;
		} catch (error) {
			console.error('Error validating profanity:', error);
			if (error.name === 'AbortError') {
				console.error('Request timed out');
			}
			return false;
		}
	}

	async function validateImageProfanity(files) {
		if (!files || files.length === 0) return false;

		const formData = new FormData();
		Array.from(files).forEach(file => {
			formData.append('postImages[]', file);
		});

		try {
			const controller = new AbortController();
			const timeoutId = setTimeout(() => {
				controller.abort();
			}, 5000);

			const response = await fetch('/forum/validate-image', {
				method: 'POST',
				body: formData,
				signal: controller.signal
			});

			clearTimeout(timeoutId);

			if (!response.ok) {
				throw new Error('Failed to validate image profanity');
			}

			const result = await response.json();
			return result.isInappropriate;
		} catch (error) {
			console.error('Error validating image profanity:', error);
			if (error.name === 'AbortError') {
				console.error('Request timed out');
			}
			return false;
		}
	}