Ember.Application.initializer({
  name: 'simple-auth-token',
  before: 'simple-auth',
  initialize: function(container) {
    container.register('simple-auth-authorizer:token', SimpleAuthToken.TokenAuthorizer);
    container.register('simple-auth-authenticator:token', SimpleAuthToken.TokenAuthenticator);
    container.register('simple-auth-authenticator:jwt', SimpleAuthToken.JWTAuthenticator);
  }
});
