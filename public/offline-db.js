// IndexedDB wrapper for offline storage
const DB_NAME = 'StreetPOS_OfflineDB';
const DB_VERSION = 1;

class OfflineDB {
    constructor() {
        this.db = null;
    }

    // Initialize the database
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onerror = () => {
                console.error('❌ Failed to open database');
                reject(request.error);
            };

            request.onsuccess = () => {
                this.db = request.result;
                console.log('✅ Database opened successfully');
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create object stores if they don't exist
                if (!db.objectStoreNames.contains('products')) {
                    const productsStore = db.createObjectStore('products', { keyPath: 'id' });
                    productsStore.createIndex('user_id', 'user_id', { unique: false });
                    console.log('📦 Created products store');
                }

                if (!db.objectStoreNames.contains('pendingSales')) {
                    db.createObjectStore('pendingSales', { keyPath: 'id', autoIncrement: true });
                    console.log('📦 Created pendingSales store');
                }

                if (!db.objectStoreNames.contains('offlineQueue')) {
                    db.createObjectStore('offlineQueue', { keyPath: 'id', autoIncrement: true });
                    console.log('📦 Created offlineQueue store');
                }
            };
        });
    }

    // Save products to IndexedDB
    async saveProducts(products) {
        try {
            if (!this.db) await this.init();

            const transaction = this.db.transaction(['products'], 'readwrite');
            const store = transaction.objectStore('products');

            // Clear existing products first
            await store.clear();

            // Add all products with error handling
            for (const product of products) {
                try {
                    store.put(product);
                } catch (err) {
                    console.error('Failed to save product:', product.name, err);
                }
            }

            return new Promise((resolve, reject) => {
                transaction.oncomplete = () => {
                    console.log('✅ Products saved to IndexedDB:', products.length);
                    resolve();
                };
                transaction.onerror = () => {
                    console.error('❌ Failed to save products:', transaction.error);
                    reject(transaction.error);
                };
                transaction.onabort = () => {
                    console.error('❌ Transaction aborted');
                    reject(new Error('Transaction aborted'));
                };
            });
        } catch (err) {
            console.error('❌ SaveProducts failed:', err);
            throw err;
        }
    }

    // Get all products from IndexedDB
    async getProducts() {
        try {
            if (!this.db) await this.init();

            const transaction = this.db.transaction(['products'], 'readonly');
            const store = transaction.objectStore('products');
            const request = store.getAll();

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    console.log('📦 Retrieved products:', request.result.length);
                    resolve(request.result);
                };
                request.onerror = () => {
                    console.error('❌ Failed to get products:', request.error);
                    reject(request.error);
                };
            });
        } catch (err) {
            console.error('❌ GetProducts failed:', err);
            // Return empty array as fallback
            return [];
        }
    }

    // Add a pending sale to IndexedDB
    async addPendingSale(saleData) {
        try {
            if (!this.db) await this.init();

            const transaction = this.db.transaction(['pendingSales'], 'readwrite');
            const store = transaction.objectStore('pendingSales');

            // Add timestamp
            saleData.timestamp = new Date().toISOString();
            saleData.synced = false;

            const request = store.add(saleData);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    console.log('✅ Pending sale added to IndexedDB');
                    resolve(request.result);
                };
                request.onerror = () => {
                    console.error('❌ Failed to add pending sale:', request.error);
                    reject(request.error);
                };
                transaction.onabort = () => {
                    console.error('❌ Transaction aborted');
                    reject(new Error('Transaction aborted'));
                };
            });
        } catch (err) {
            console.error('❌ AddPendingSale failed:', err);
            throw err;
        }
    }

    // Get all pending sales
    async getPendingSales() {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readonly');
        const store = transaction.objectStore('pendingSales');
        const request = store.getAll();

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('📦 Retrieved pending sales:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('❌ Failed to get pending sales');
                reject(request.error);
            };
        });
    }

    // Remove a pending sale after successful sync
    async removePendingSale(id) {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readwrite');
        const store = transaction.objectStore('pendingSales');
        const request = store.delete(id);

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('✅ Pending sale removed from IndexedDB');
                resolve();
            };
            request.onerror = () => {
                console.error('❌ Failed to remove pending sale');
                reject(request.error);
            };
        });
    }

    // Clear all pending sales (after successful sync)
    async clearPendingSales() {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readwrite');
        const store = transaction.objectStore('pendingSales');
        const request = store.clear();

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('✅ All pending sales cleared');
                resolve();
            };
            request.onerror = () => {
                console.error('❌ Failed to clear pending sales');
                reject(request.error);
            };
        });
    }

    // Update product stock locally (for offline mode)
    async updateProductStock(productId, newStock) {
        try {
            if (!this.db) await this.init();

            const transaction = this.db.transaction(['products'], 'readwrite');
            const store = transaction.objectStore('products');
            const request = store.get(productId);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    const product = request.result;
                    if (product) {
                        product.stock = newStock;
                        const updateRequest = store.put(product);

                        updateRequest.onsuccess = () => {
                            console.log('✅ Product stock updated locally');
                            resolve(product);
                        };
                        updateRequest.onerror = () => {
                            console.error('❌ Failed to update product stock:', updateRequest.error);
                            reject(updateRequest.error);
                        };
                    } else {
                        console.warn('⚠️ Product not found for stock update:', productId);
                        reject(new Error('Product not found'));
                    }
                };
                request.onerror = () => {
                    console.error('❌ Failed to get product for update:', request.error);
                    reject(request.error);
                };
                transaction.onabort = () => {
                    console.error('❌ Transaction aborted');
                    reject(new Error('Transaction aborted'));
                };
            });
        } catch (err) {
            console.error('❌ UpdateProductStock failed:', err);
            throw err;
        }
    }
}

// Create a singleton instance
const offlineDB = new OfflineDB();

// Initialize on load
if (typeof window !== 'undefined') {
    window.addEventListener('load', () => {
        offlineDB.init().catch(err => {
            console.error('Failed to initialize offline database:', err);
        });
    });
}
