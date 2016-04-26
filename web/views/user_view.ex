defmodule Pickems.UserView do
  use Pickems.Web, :view

  def render("index.json", %{users: users}) do
    %{data: render_many(users, Pickems.UserView, "user.json")}
  end

  def render("show.json", %{user: user}) do
    %{data: render_one(user, Pickems.UserView, "user.json")}
  end

  def render("user.json", %{user: user}) do
    %{
      "type": "users",
      "id": user.id,
      "attributes": %{
        "name": user.name,
        "email": user.email,
        "admin": user.admin
      }
    }
  end
end
