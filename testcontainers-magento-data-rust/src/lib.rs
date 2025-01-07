#![allow(async_fn_in_trait)]
//! # 🐳 Test-Containers for Quick Magento Development
//!
//! Allows to quickly spin up ready to use data containers with pre-build Magento configurations
//!
//! ## Magento 2.4.7-p3 with Sample Data
//!
//! Here is a quick example of the test with MySQL container with sample data for 2.4.7-p3 release
//!
//! ```rust
//! use sqlx::mysql::MySqlPool;
//! use testcontainers::runners::AsyncRunner;
//! use testcontainers_magento_data::core::ImageBuilder;
//! use testcontainers_magento_data::images::{DbContainer, DbConnection};
//!
//! #[tokio::test]
//! async fn starts_container_with_sample_data() {
//!     let container = DbContainer::mysql()
//!         .with_sample_data()
//!         .with_version("2.4.7-p3")
//!         .start().await.unwrap();
//!
//!
//!     let connection = MySqlPool::connect(
//!         &container.connection_url().await.unwrap()
//!     ).await.unwrap();
//!
//!     let total: i64 = sqlx::query_scalar(
//!         "SELECT COUNT(*) FROM catalog_product_entity"
//!     ).fetch_one(&connection).await.unwrap();
//!
//!     assert_eq!(total, 2040);
//! }
//! ```
pub mod core;
pub mod images;
pub mod runners;
