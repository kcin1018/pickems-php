defmodule Pickems.Repo.Migrations.CreateTeam do
  use Ecto.Migration

  def change do
    create table(:teams) do
      add :name, :string
      add :slug, :string
      add :paid, :boolean

      timestamps
    end

    # Unique email address constraint, via DB index
    create index(:teams, [:name], unique: true)
  end
end
