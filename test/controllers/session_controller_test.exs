defmodule Pickems.SessionControllerTest do
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

  test "able to login as a created user", %{conn: conn} do
    conn = post conn, registration_path(conn, :create), %{data: %{type: "users",
      attributes: @valid_attrs
      }}
    assert json_response(conn, 201)["data"]["id"]
    assert Repo.get_by(User, %{email: @valid_attrs[:email]})

    conn = post conn, login_path(conn, :create), %{grant_type: "password", username: @valid_attrs[:email], password: @valid_attrs[:password]}
    assert json_response(conn, 200)["access_token"]
  end

  test "invalid login with return a 401", %{conn: conn} do
    conn = post conn, registration_path(conn, :create), %{data: %{type: "users",
      attributes: @valid_attrs
      }}

    conn = post conn, login_path(conn, :create), %{grant_type: "password", username: "not@exist.com", password: @valid_attrs[:password]}
    assert json_response(conn, 401)
  end
end
