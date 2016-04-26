defmodule Pickems.TeamTest do
  use Pickems.ModelCase

  alias Pickems.Team

  @valid_attrs %{name: "Test Team", paid: false}
  @invalid_attrs %{}

  test "changeset with valid attributes" do
    changeset = Team.changeset(%Team{}, @valid_attrs)
    assert changeset.valid?
  end

  test "changeset with invalid attributes" do
    changeset = Team.changeset(%Team{}, @invalid_attrs)
    refute changeset.valid?
  end
end
