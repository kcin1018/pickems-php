defmodule Pickems.RegistrationControllerTest do
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

  test "creates and renders resource when data is valid", %{conn: conn} do
    conn = post conn, registration_path(conn, :create), %{data: %{type: "users",
      attributes: @valid_attrs
      }}
    assert json_response(conn, 201)["data"]["id"]
    assert Repo.get_by(User, %{email: @valid_attrs[:email]})
  end

  test "does not create resource and renders errors when data is invalid", %{conn: conn} do
    assert_error_sent 400, fn ->
      # conn = post conn, registration_path(conn, :create),  %{data: %{type: "users",
      post conn, registration_path(conn, :create),  %{data: %{type: "users",
        attributes: @invalid_attrs
      }}
    end
  end

end
