defmodule Pickems.UserController do
  use Pickems.Web, :controller

  alias Pickems.User
  plug Guardian.Plug.EnsureAuthenticated, handler: Pickems.AuthErrorHandler

  def current(conn, _) do
    user = conn
    |> Guardian.Plug.current_resource

    conn
    |> render(Pickems.UserView, "show.json", user: user)
  end
end
