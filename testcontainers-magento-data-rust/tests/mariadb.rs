use sqlx::MySqlPool;
use testcontainers::runners::AsyncRunner;
use testcontainers_magento_data::core::ImageBuilder;
use testcontainers_magento_data::images::{DbConnection, DbContainer};

#[tokio::test]
async fn starts_container_with_sample_data() {
    let container = DbContainer::mariadb()
        .with_sample_data()
        .with_version("2.4.7-p2")
        .start()
        .await
        .unwrap();

    let connection = MySqlPool::connect(&container.connection_url().await.unwrap())
        .await
        .unwrap();

    let total: i64 = sqlx::query_scalar("SELECT COUNT(*) FROM catalog_product_entity")
        .fetch_one(&connection)
        .await
        .unwrap();

    assert_eq!(total, 2040);
}
