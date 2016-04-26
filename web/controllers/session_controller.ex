defmodule Pickems.SessionController do
  use Pickems.Web, :controller

  import Ecto.Query, only: [where: 2]
  import Comeonin.Bcrypt
  import Logger

  alias Pickems.Repo
  alias Pickems.User

  def create(conn, %{"grant_type" => "password",
    "username" => username,
    "password" => password}) do

    try do
      # Attempt to retrieve exactly one user from the DB, whose
      #   email matches the one provided with the login request
      user = User
      |> where(email: ^username)
      |> Repo.one!

      cond do
        checkpw(password, user.password_hash) ->
          # Encode a JWT
          { :ok, jwt, _} = Guardian.encode_and_sign(user, :token, %{})
          # Successful login
          Logger.info "User " <> username <> " just logged in"

          conn
          |> json(%{access_token: jwt}) # Return token to the client

        true ->
          # Unsuccessful login
          Logger.info "User " <> username <> " just failed to login"

          conn
          |> put_status(401)
          |> render(Pickems.ErrorView, "401.json") # 401
      end
    rescue
      e ->
        # IO.inspect e # Print error to the console for debugging
        Logger.info "Unexpected error while attempting to login user " <> username

        conn
        |> put_status(401)
        |> render(Pickems.ErrorView, "401.json") # 401
    end
  end

  def create(conn, %{"grant_type" => _}) do
    ## Handle unknown grant type
    Logger.error "Unsupported grant_type" <> conn
    throw "Unsupported grant_type"
  end
end
