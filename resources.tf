resource "aws_cognito_user_pool" "dafed" {
  name = var.cognito_user_pool_name

  password_policy {
    minimum_length    = 12
    require_lowercase = true
    require_uppercase = true
    require_numbers   = true
    require_symbols   = false
  }

  admin_create_user_config {
    allow_admin_create_user_only = true
  }

  # https://stackoverflow.com/a/73434724/5873008
  username_attributes      = ["email"]
  auto_verified_attributes = ["email"]

  account_recovery_setting {
    recovery_mechanism {
      name     = "verified_email"
      priority = 1
    }
  }
}

resource "aws_cognito_user_pool_domain" "dafed" {
  domain       = var.cognito_user_pool_domain
  user_pool_id = aws_cognito_user_pool.dafed.id
}

resource "aws_cognito_user_pool_ui_customization" "example" {
  user_pool_id = aws_cognito_user_pool_domain.dafed.user_pool_id
  image_file   = filebase64("resources/logo.png")
}

resource "aws_cognito_user_pool_client" "web" {
  name         = "web"
  user_pool_id = aws_cognito_user_pool.dafed.id

  generate_secret         = true
  enable_token_revocation = true
  access_token_validity   = 60
  id_token_validity       = 60
  refresh_token_validity  = 1
  token_validity_units {
    access_token  = "minutes"
    id_token      = "minutes"
    refresh_token = "days"
  }

  allowed_oauth_flows_user_pool_client = true
  allowed_oauth_flows = [
    "code",
    "implicit"
  ]
  allowed_oauth_scopes = [
    "email",
    "openid",
    "phone",
    "profile"
  ]
  supported_identity_providers = [
    "COGNITO"
  ]

  explicit_auth_flows = [
    "ALLOW_REFRESH_TOKEN_AUTH",
    "ALLOW_USER_PASSWORD_AUTH"
  ]

  callback_urls = var.web_callback_urls
  logout_urls   = var.web_logout_urls
}

resource "aws_cognito_user_pool_client" "system" {
  name         = "system"
  user_pool_id = aws_cognito_user_pool.dafed.id

  generate_secret         = true
  enable_token_revocation = true
  access_token_validity   = 60
  id_token_validity       = 60
  refresh_token_validity  = 1
  token_validity_units {
    access_token  = "minutes"
    id_token      = "minutes"
    refresh_token = "days"
  }

  supported_identity_providers = [
    "COGNITO"
  ]

  explicit_auth_flows = [
    "ADMIN_NO_SRP_AUTH"
  ]
}

resource "aws_cognito_user" "players" {
  count = length(var.cognito_user_pool_members)

  user_pool_id = aws_cognito_user_pool.dafed.id
  username     = var.cognito_user_pool_members[count.index].email
  password     = var.cognito_user_pool_members[count.index].password

  attributes = {
    email          = var.cognito_user_pool_members[count.index].email
    email_verified = true
  }
}
