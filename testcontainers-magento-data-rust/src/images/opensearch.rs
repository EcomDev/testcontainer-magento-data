use crate::core::{ImageBuilderTarget, ImageName, ImageTag};
use std::borrow::Cow;
use testcontainers::core::WaitFor;
use testcontainers::Image;

pub struct OpenSearchContainer {
    image_name: ImageName,
    image_tag: ImageTag,
}

impl Default for OpenSearchContainer {
    fn default() -> Self {
        Self::new()
    }
}

impl OpenSearchContainer {
    pub fn new() -> Self {
        Self {
            image_name: ImageName::new("opensearch"),
            image_tag: ImageTag::default(),
        }
    }
}

impl Image for OpenSearchContainer {
    fn name(&self) -> &str {
        self.image_name.as_ref()
    }

    fn tag(&self) -> &str {
        self.image_tag.as_ref()
    }

    fn ready_conditions(&self) -> Vec<WaitFor> {
        vec![WaitFor::message_on_stdout(
            "Cluster health status changed from [RED]",
        )]
    }

    fn env_vars(
        &self,
    ) -> impl IntoIterator<Item = (impl Into<Cow<'_, str>>, impl Into<Cow<'_, str>>)> {
        [
            ("discovery.type", "single-node"),
            ("DISABLE_SECURITY_PLUGIN", "true"),
        ]
    }
}

impl ImageBuilderTarget for OpenSearchContainer {
    fn with_image_tag(self, image_tag: impl FnOnce(ImageTag) -> ImageTag) -> Self {
        Self {
            image_tag: image_tag(self.image_tag),
            ..self
        }
    }

    fn with_image_name(self, image_name: impl FnOnce(ImageName) -> ImageName) -> Self {
        Self {
            image_name: image_name(self.image_name),
            ..self
        }
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    use crate::core::ImageBuilder;

    #[test]
    fn creates_container_builder_for_mysql_image() {
        let image = OpenSearchContainer::new().with_sample_data();

        assert_eq!(
            "ghcr.io/ecomdev/testcontainer-magento-data/opensearch",
            image.name()
        );
        assert_eq!("latest-sampledata", image.tag());
    }

    #[test]
    fn creates_container_builder_for_mariadb_image() {
        let image = OpenSearchContainer::new().with_version("2.4.8-beta1");

        assert_eq!(
            "ghcr.io/ecomdev/testcontainer-magento-data/opensearch",
            image.name()
        );
        assert_eq!("2.4.8-beta1", image.tag());
    }
}
