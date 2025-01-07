pub const DEFAULT_IMAGE_REPOSITORY: &str = "ghcr.io/ecomdev/testcontainer-magento-data";

pub trait ImageRepository {
    fn image_path(&self, image: &str) -> String;
}

impl<T> ImageRepository for T
where
    T: std::fmt::Display,
{
    fn image_path(&self, image: &str) -> String {
        format!("{self}/{image}")
    }
}
