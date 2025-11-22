<x-app-layout>
    <div class="container mx-auto" x-data="productTryOn()">
        <div class="grid gap-6 grid-cols-1 lg:grid-cols-5">
            <!-- Product Image -->
            <div class="lg:col-span-3">
                <img :src="productImage" alt="Product Image" class="w-full h-auto">
            </div>

            <!-- Product Info -->
            <div class="lg:col-span-2">
                <h1 class="text-lg font-semibold">{{ $product->title }}</h1>
                <div class="text-xl font-bold mb-6">${{ $product->price }}</div>

                <!-- User Upload -->
                <div class="mb-4">
                    <label class="block font-bold mb-2">Upload Your Photo:</label>
                    <input type="file" accept="image/*" @change="handlePersonImage($event)">
                </div>

                <!-- Garment Upload (optional, for testing high-res) -->
                <div class="mb-4">
                    <label class="block font-bold mb-2">Upload Garment Photo (≥512px):</label>
                    <input type="file" accept="image/*" @change="handleGarmentImage($event)">
                </div>

                <button @click="generateTryOn()" class="btn-primary py-2 px-4"
                    :disabled="!personImage || !garmentImage">
                    Try On
                </button>

                <div class="mt-6">
                    <h2 class="font-bold mb-2">Result:</h2>
                    <img :src="tryOnResult" alt="Try-On Result" class="w-full h-auto" x-show="tryOnResult">

                    <!-- Loading and Status Display -->
                    <div x-show="loading" class="space-y-3 p-4 bg-blue-50 rounded-lg">
                        <p class="text-blue-700 font-medium">Processing Virtual Try-On...</p>
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                            <div class="flex-1">
                                <p class="text-sm text-blue-600" x-text="jobId ? 'Job ID: ' + jobId : 'Submitting...'">
                                </p>
                                <p class="text-xs text-blue-500 mt-1">This usually takes 30-120 seconds</p>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="w-full bg-blue-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000"
                                :style="'width: ' + Math.min(100, (attempts / 60) * 100) + '%'"></div>
                        </div>
                    </div>

                    <!-- Error Display -->
                    <p x-show="error" class="text-red-600 bg-red-50 p-3 rounded" x-text="error"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function productTryOn() {
            return {
                productImage: '{{ $product->image }}',
                personImage: null,
                garmentImage: null,
                tryOnResult: null,
                loading: false,
                jobId: null,
                error: null,
                attempts: 0,

                handlePersonImage(event) {
                    this.personImage = event.target.files[0];
                    this.error = null;
                },

                handleGarmentImage(event) {
                    this.garmentImage = event.target.files[0];
                    this.error = null;
                },

                async generateTryOn() {
                    if (!this.personImage || !this.garmentImage) {
                        this.error = 'Please upload both your photo and the garment photo';
                        return;
                    }

                    this.loading = true;
                    this.tryOnResult = null;
                    this.error = null;
                    this.attempts = 0;

                    try {
                        console.log('Starting try-on process...');

                        const formData = new FormData();
                        formData.append('user_image', this.personImage);
                        formData.append('outfit_image', this.garmentImage);
                        formData.append('fast_mode', 'true');

                        console.log('Sending request to /tryon...');

                        const response = await fetch('/tryon', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        console.log('Response status:', response.status);

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.error || `Server error: ${response.status}`);
                        }

                        this.jobId = data.jobId;
                        console.log('Job submitted successfully:', this.jobId);

                        // Start polling for results
                        await this.pollForResult();

                    } catch (err) {
                        console.error('Try-on error:', err);
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async pollForResult() {
                    let status = 'queued';
                    this.attempts = 0;
                    const maxAttempts = 60; // Increased to 120 seconds (60 attempts * 2 seconds)

                    // Define all processing statuses that mean we should keep polling
                    const processingStatuses = [
                        'queued',
                        'processing',
                        'processing_from_queue',
                        'queued_for_processing'
                    ];

                    while (processingStatuses.includes(status) && this.attempts < maxAttempts) {
                        await new Promise(r => setTimeout(r, 2000));
                        this.attempts++;

                        try {
                            console.log(`Polling attempt ${this.attempts} for job ${this.jobId}`);

                            const statusResp = await fetch(`/tryon/status/${this.jobId}`);
                            const statusData = await statusResp.json();

                            status = statusData.status;
                            console.log(`Status: ${status}`);
                            console.log('Full status data:', statusData);

                            if (status === 'completed') {
                                if (statusData.imageUrl) {
                                    this.tryOnResult = statusData.imageUrl;
                                    console.log('Try-on completed! Image URL:', statusData.imageUrl);
                                } else if (statusData.imageBase64) {
                                    this.tryOnResult = `data:image/png;base64,${statusData.imageBase64}`;
                                    console.log('Try-on completed! Using base64 image');
                                } else {
                                    throw new Error('No image data received from API');
                                }
                                break;
                            }

                            if (status === 'failed') {
                                const errorMsg = statusData.error || statusData.message || 'Try-on processing failed';
                                console.error('Try-on failed:', errorMsg);
                                throw new Error(errorMsg);
                            }

                            // Show progress to user
                            if (statusData.message) {
                                console.log('Progress message:', statusData.message);
                            }

                        } catch (err) {
                            console.warn('Polling error:', err.message);
                            // Continue polling on network errors
                        }
                    }

                    if (processingStatuses.includes(status)) {
                        this.error =
                            'Try-on processing is taking longer than expected. The job is still processing. Please check back later or try again.';
                        console.warn('Polling timed out, job might still be processing');
                    }
                }
            }
        }
    </script>
</x-app-layout>
