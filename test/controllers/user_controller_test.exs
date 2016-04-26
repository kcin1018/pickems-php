defmodule Pickems.UserControllerTest do
  use Pickems.ConnCase

  alias Pickems.Repo
  alias Pickems.User

  @valid_attrs %{
    name: "Test User",
    admin: false,
    email: "nick@example.com",
    password: "fqhi12hrrfasf",
    password_confirmation: "fqhi12hrrfasf"
  }

  @invalid_attrs %{}

  setup %{conn: conn} do
    {:ok, conn: put_req_header(conn, "accept", "application/json")}
  end

  test "shows currently logged in user", %{conn: conn} do
    changeset = User.changeset(%User{}, @valid_attrs)
    Repo.insert(changeset)

    conn = post conn, login_path(conn, :create), %{grant_type: "password", username: @valid_attrs[:email], password: @valid_attrs[:password]}
    token = json_response(conn, 200)["access_token"]

    conn = conn()
      |> put_req_header("authorization", token)
      |> get("/api/user/current")

    assert json_response(conn, 200)["data"]["id"]
  end
end
