#[derive(Debug, PartialEq)]
pub struct ImageTag {
    inner: ImageTagInner,
    rendered: String,
}

impl Default for ImageTag {
    fn default() -> Self {
        Self::new("latest")
    }
}

impl ImageTag {
    pub(crate) fn new(version: impl AsRef<str>) -> Self {
        Self::from_inner(ImageTagInner::new(version))
    }

    pub(crate) fn with_variation(self, variation: impl AsRef<str>) -> Self {
        Self::from_inner(self.inner.with_variation(variation))
    }

    pub(crate) fn with_version(self, version: impl AsRef<str>) -> Self {
        Self::from_inner(self.inner.with_version(version))
    }

    fn from_inner(inner: ImageTagInner) -> Self {
        let rendered = inner.render();
        Self { inner, rendered }
    }
}

impl AsRef<str> for ImageTag {
    fn as_ref(&self) -> &str {
        &self.rendered
    }
}

#[derive(Debug, Clone, PartialEq)]
enum ImageTagInner {
    Version(String),
    VersionVariation(String, String),
}

impl ImageTagInner {
    fn new(version: impl AsRef<str>) -> ImageTagInner {
        Self::Version(version.as_ref().to_string())
    }

    fn with_variation(self, variation: impl AsRef<str>) -> Self {
        let variation = variation.as_ref().to_string();
        match self {
            Self::Version(version) => Self::VersionVariation(version, variation),
            Self::VersionVariation(version, _) => Self::VersionVariation(version, variation),
        }
    }

    fn with_version(self, version: impl AsRef<str>) -> Self {
        let version = version.as_ref().to_string();
        match self {
            Self::Version(_) => Self::Version(version),
            Self::VersionVariation(_, tag) => Self::VersionVariation(version, tag),
        }
    }

    fn render(&self) -> String {
        match self {
            Self::Version(version) => version.to_owned(),
            Self::VersionVariation(version, tag) => {
                format!("{version}-{tag}")
            }
        }
    }
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn defaults_to_latest_version() {
        let tag = ImageTag::default();
        assert_eq!(tag.as_ref(), "latest");
    }

    #[test]
    fn overrides_version_with_different_string_values() {
        assert_eq!(ImageTag::default().with_version("1.0.0").as_ref(), "1.0.0");

        assert_eq!(
            ImageTag::default()
                .with_version(String::from("1.0.1"))
                .as_ref(),
            "1.0.1"
        );
    }

    #[test]
    fn uses_variation_if_provided_and_version_is_not_latest() {
        assert_eq!(
            ImageTag::default().with_variation("test").as_ref(),
            "latest-test"
        );

        assert_eq!(
            ImageTag::default()
                .with_variation("test")
                .with_version("2.4.7-p2")
                .as_ref(),
            "2.4.7-p2-test"
        );
    }
}
