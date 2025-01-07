use crate::core::{ImageBuilderTarget, ImageName, ImageTag};
use testcontainers::core::{ExecCommand, WaitFor};
use testcontainers::{ContainerAsync, Image, TestcontainersError};
use tokio::io::AsyncReadExt;

pub struct DbContainer {
    image_name: ImageName,
    image_tag: ImageTag,
}

pub trait DbConnection {
    async fn connection_url(&self) -> Result<String, TestcontainersError>;
}

impl DbConnection for ContainerAsync<DbContainer> {
    async fn connection_url(&self) -> Result<String, TestcontainersError> {
        let mut result = self.exec(ExecCommand::new(vec!["env"])).await?;

        let mut output = String::new();
        result.stdout().read_to_string(&mut output).await?;

        let env = env_file_reader::read_str(output.as_str()).map_err(TestcontainersError::other)?;

        let mut connection_url = String::new();

        connection_url.push_str("mysql://");
        match env.get("MYSQL_USER") {
            Some(user) => connection_url.push_str(user),
            None => connection_url.push_str("root"),
        }

        if let Some(password) = env.get("MYSQL_PASSWORD") {
            connection_url.push_str(&format!(":{}", password));
        }

        connection_url.push('@');
        connection_url.push_str(&self.get_host().await?.to_string());
        connection_url.push(':');
        connection_url.push_str(&self.get_host_port_ipv4(3306).await?.to_string());
        connection_url.push('/');

        if let Some(database) = env.get("MYSQL_DATABASE") {
            connection_url.push_str(database);
        }

        Ok(connection_url)
    }
}

impl DbContainer {
    fn new(image_name: ImageName) -> Self {
        Self {
            image_name,
            image_tag: ImageTag::default(),
        }
    }

    pub fn mysql() -> Self {
        Self::new(ImageName::new("mysql"))
    }

    pub fn mariadb() -> Self {
        Self::new(ImageName::new("mariadb"))
    }
}

impl Image for DbContainer {
    fn name(&self) -> &str {
        self.image_name.as_ref()
    }

    fn tag(&self) -> &str {
        self.image_tag.as_ref()
    }

    fn ready_conditions(&self) -> Vec<WaitFor> {
        vec![WaitFor::message_on_stderr("ready for connections")]
    }
}

impl ImageBuilderTarget for DbContainer {
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
        let image = DbContainer::mysql().with_sample_data();

        assert_eq!(
            "ghcr.io/ecomdev/testcontainer-magento-data/mysql",
            image.name()
        );
        assert_eq!("latest-sampledata", image.tag());
    }

    #[test]
    fn creates_container_builder_for_mariadb_image() {
        let image = DbContainer::mariadb().with_version("2.4.8-beta1");

        assert_eq!(
            "ghcr.io/ecomdev/testcontainer-magento-data/mariadb",
            image.name()
        );
        assert_eq!("2.4.8-beta1", image.tag());
    }
}
