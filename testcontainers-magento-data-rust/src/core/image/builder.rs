use crate::core::{ImageName, ImageRepository, ImageTag};

pub trait ImageBuilder: Sized {
    fn with_sample_data(self) -> Self;
    fn with_variation(self, variation: impl AsRef<str>) -> Self;
    fn with_version(self, version: impl AsRef<str>) -> Self;
    fn with_repository(self, repository: impl ImageRepository) -> Self;
}

pub trait ImageBuilderTarget: Sized {
    fn with_image_tag(self, image_tag: impl FnOnce(ImageTag) -> ImageTag) -> Self;
    fn with_image_name(self, image_name: impl FnOnce(ImageName) -> ImageName) -> Self;
}

impl<T> ImageBuilder for T
where
    T: ImageBuilderTarget,
{
    fn with_sample_data(self) -> Self {
        self.with_image_tag(|tag| tag.with_variation("sampledata"))
    }

    fn with_variation(self, variation: impl AsRef<str>) -> Self {
        self.with_image_tag(|tag| tag.with_variation(variation))
    }

    fn with_version(self, version: impl AsRef<str>) -> Self {
        self.with_image_tag(|tag| tag.with_version(version))
    }

    fn with_repository(self, repository: impl ImageRepository) -> Self {
        self.with_image_name(|name| name.with_repository(repository))
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    use std::borrow::Cow;

    struct TestImage(ImageName, ImageTag);

    impl ImageBuilderTarget for TestImage {
        fn with_image_tag(self, image_tag: impl FnOnce(ImageTag) -> ImageTag) -> Self {
            Self(self.0, image_tag(self.1))
        }

        fn with_image_name(self, image_name: impl FnOnce(ImageName) -> ImageName) -> Self {
            Self(image_name(self.0), self.1)
        }
    }

    impl TestImage {
        fn full_name(&self) -> Cow<str> {
            Cow::Borrowed(self.0.as_ref()) + Cow::Borrowed(":") + Cow::Borrowed(self.1.as_ref())
        }
    }

    #[test]
    fn allows_providing_sample_data_variation() {
        assert_eq!(
            TestImage(ImageName::new("mysql"), ImageTag::default())
                .with_sample_data()
                .full_name(),
            "ghcr.io/ecomdev/testcontainer-magento-data/mysql:latest-sampledata",
        );
    }

    #[test]
    fn allows_custom_version() {
        assert_eq!(
            TestImage(ImageName::new("mariadb"), ImageTag::default())
                .with_version("2.4.8-beta1")
                .full_name(),
            "ghcr.io/ecomdev/testcontainer-magento-data/mariadb:2.4.8-beta1",
        );
    }

    #[test]
    fn allows_custom_variation() {
        assert_eq!(
            TestImage(ImageName::new("mariadb"), ImageTag::default())
                .with_version("2.4.8")
                .with_variation("generated-small")
                .full_name(),
            "ghcr.io/ecomdev/testcontainer-magento-data/mariadb:2.4.8-generated-small",
        );
    }

    #[test]
    fn allows_custom_repository() {
        assert_eq!(
            TestImage(ImageName::new("test"), ImageTag::default())
                .with_repository("wardenenv/data")
                .full_name(),
            "wardenenv/data/test:latest",
        );
    }
}
