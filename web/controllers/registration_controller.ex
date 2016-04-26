defmodule Pickems.RegistrationController do
  use Pickems.Web, :controller

  alias Pickems.User

  def create(conn, %{"data" => %{"type" => "users",
    "attributes" => %{"email" => email,
      "admin" => admin,
      "name" => name,
      "password" => password,
      "password_confirmation" => password_confirmation}}}) do

    changeset = User.changeset %User{}, %{email: email,
      name: name,
      admin: admin,
      password_confirmation: password_confirmation,
      password: password}

    case Repo.insert changeset do
      {:ok, user} ->
        conn
        |> put_status(:created)
        |> render(Pickems.UserView, "show.json", user: user)
      {:error, changeset} ->
        conn
        |> put_status(:unprocessable_entity)
        |> render(Pickems.ChangesetView, "error.json", changeset: changeset)
    end
  end
end
